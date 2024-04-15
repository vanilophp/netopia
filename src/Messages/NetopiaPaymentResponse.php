<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Messages;

use Konekt\Enum\Enum;
use SimpleXMLElement;
use Vanilo\Netopia\Models\NetopiaAction;
use Vanilo\Netopia\Models\NetopiaErrorCode;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\PaymentStatus;
use Vanilo\Payment\Models\PaymentStatusProxy;

class NetopiaPaymentResponse implements PaymentResponse
{
    /**
     * The action attempted by Netopia (or MobilPay). The possible actions are below:
     * - [new, paid_pending, confirmed_pending, paid, confirmed, credit, canceled]
     * It's not the transaction status since actions can either fail or succeed
     */
    public NetopiaAction $action;

    /**
     * The error code states whether the action has been successful or not:
     *  - 0 (zero) => the action has succeeded
     *  - Non-zero => the action has failed
     */
    private int $errorCode;

    private string $message;

    private string $paymentId;

    private string $crc;

    private float $submittedAmountInOriginalCurrency;

    private float $submittedAmount;

    private float $processedAmount;

    private ?PaymentStatus $status = null;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->paymentId = (string) $xml->attributes()->id[0];
        $this->errorCode = (int) $xml->mobilpay->error->attributes()->code[0];
        $this->crc = (string) $xml->mobilpay->attributes()->crc[0];

        $this->processedAmount = (float) $xml->mobilpay->processed_amount[0];
        $this->submittedAmount = (float) $xml->mobilpay->original_amount[0];
        $this->submittedAmountInOriginalCurrency = (float) $xml->invoice->attributes()->amount[0];

        $this->action = NetopiaAction::create(isset($xml->mobilpay->action[0]) ? trim((string) $xml->mobilpay->action[0]) : null);
        $this->message = $this->obtainMessage($xml);
    }

    public function wasSuccessful(): bool
    {
        return 0 === $this->errorCode;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCrc(): string
    {
        return $this->crc;
    }

    public function getTransactionId(): ?string
    {
        // Netopia treats the passed id as transaction id
        // We follow that convention to comply with it
        return $this->paymentId;
    }

    public function getTransactionAmount(): float
    {
        return $this->getAmountPaid();
    }

    public function getAmountPaid(): ?float
    {
        $sign = ($this->action->isCredit() || $this->action->isCanceled()) ? -1 : 1;

        /** Settlement and original currencies match, the happy path: */
        if ($this->submittedAmountInOriginalCurrency === $this->submittedAmount) {
            return $sign * $this->processedAmount;
        }

        /** Currencies differ, but a full payment was made: */
        if ($this->processedAmount === $this->submittedAmount) {
            return $sign * $this->submittedAmountInOriginalCurrency;
        }

        /** Currencies differ, partial payment was made. Calculate back: */
        $rate = $this->submittedAmount / $this->submittedAmountInOriginalCurrency;

        return $sign * round($this->processedAmount / $rate, 2);
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getStatus(): PaymentStatus
    {
        if (null === $this->status) {
            if (!$this->wasSuccessful()) {
                $this->status = PaymentStatusProxy::DECLINED();
            } else {
                switch ($this->action) {
                    case NetopiaAction::PAID:
                        /** From original documentation:
                         *   Deschisa – atributul elementului action este paid . Banii sunt rezervati pe
                         *   card, nu a avut loc transfer bancar
                         */
                        $this->status = PaymentStatusProxy::AUTHORIZED();
                        break;

                    case NetopiaAction::CONFIRMED:
                        /** From original documentation:
                         *    Platita/Confirmata - atributul elementului action este confirmed. Banii
                         *    rezervati pe card au fost transferati si intra in procesul de decontare. Daca
                         *    mobilpay nu primeste raspuns de la pagina ta de confirmare, tranzactia
                         *    ramane in stare Platita
                         */
                        if ($this->processedAmount < $this->submittedAmount) {
                            $this->status = PaymentStatusProxy::PARTIALLY_PAID();
                        } else {
                            $this->status = PaymentStatusProxy::PAID();
                        }
                        break;

                    case NetopiaAction::CANCELED:
                        /** From original documentation:
                         *    Anulata - atributul elementului action este canceled . Banii rezervati pe
                         *    card sunt eliberati.
                         */
                        $this->status = PaymentStatusProxy::CANCELLED();
                        break;

                    case NetopiaAction::NEW:
                    case NetopiaAction::UNKNOWN:
                        /** From original documentation:
                         *    Noua - clientul a ajuns in pagina de plata, dar nu a introdus detaliile
                         *    necesare pentru initierea platii
                         */
                        $this->status = PaymentStatusProxy::PENDING();
                        break;

                    case NetopiaAction::PAID_PENDING:
                        /** From original documentation:
                         *    In asteptare - atributul elementului action este paid_pending.
                         *    Tranzactia este intr-un proces de verificare in ceea ce priveste riscul de
                         *    frauda. Banii sunt rezervati pe card, nu a avut loc transfer bancar. Este
                         *    necesara capturarea banilor!
                         */
                        $this->status = PaymentStatusProxy::ON_HOLD();
                        break;

                    case NetopiaAction::CONFIRMED_PENDING:
                        /** From original documentation:
                         *    In verificare – atributul elementului action este confirmed_pending.
                         *    Tranzactia este intr-un proces de verificare in ceea ce priveste riscul de
                         *    frauda. Banii sunt luati de pe card. Daca este acceptata, plata intra in stare
                         *    Confirmata si vei fi notificat cu un action = “confirmed”. In caz contrar,
                         *    plata intra in stare Frauda
                         */
                        $this->status = PaymentStatusProxy::ON_HOLD();
                        break;

                    case NetopiaAction::CREDIT:
                        /** From original documentation:
                         *    Creditata - atributul elementului action este credit . Banii sunt returnati
                         *    clientului (in totalitate sau partial)
                         */
                        if ($this->processedAmount < $this->submittedAmount) {
                            $this->status = PaymentStatusProxy::PARTIALLY_REFUNDED();
                        } else {
                            $this->status = PaymentStatusProxy::REFUNDED();
                        }
                        break;

                    default:
                        $this->status = PaymentStatusProxy::PENDING();
                }
            }
        }

        return $this->status;
    }

    public function getNativeStatus(): Enum
    {
        return $this->action;
    }

    /**
     * The original amount that has been submitted for payment in original
     * currency. It can be a different currency (eg. EUR, USD) than the
     * Netopia account's settlement currency which is typically RON.
     */
    public function getSubmittedAmountInOriginalCurrency(): float
    {
        return $this->submittedAmountInOriginalCurrency;
    }

    /**
     * The submitted original amount expressed in the settlement currency of the utilized
     * Netopia account. It's typically the converted RON value. If the shop's currency
     * is also RON, then it equals to the $submittedAmountInOriginalCurrency field.
     */
    public function getSubmittedAmount(): float
    {
        return $this->submittedAmount;
    }

    /**
     * The amount that has been processed, ie paid in the utilized Netopia account's
     * settlement currency, which is typically RON. To see if complete or partial
     * payment has been done, this amount must be compared to $submittedAmount
     */
    public function getProcessedAmount(): float
    {
        return $this->processedAmount;
    }

    private function obtainMessage(SimpleXMLElement $xml): string
    {
        if (isset($xml->mobilpay->error[0])) {
            $message = trim((string) $xml->mobilpay->error[0]);
            if (!empty($message)) {
                return $message;
            }
        }

        return NetopiaErrorCode::create($this->errorCode)->label();
    }
}
