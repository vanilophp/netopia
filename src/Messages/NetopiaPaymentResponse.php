<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Messages;

use SimpleXMLElement;
use Vanilo\Netopia\Models\NetopiaAction;
use Vanilo\Payment\Contracts\PaymentResponse;

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

    private string $transactionId;

    private float $submittedAmountInOriginalCurrency;

    private float $submittedAmount;

    private float $processedAmount;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->paymentId = (string) $xml->attributes()->id[0];
        $this->errorCode = (int) $xml->mobilpay->error->attributes()->code[0];
        $this->transactionId = (string) $xml->mobilpay->attributes()->crc[0];

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

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getAmountPaid(): ?float
    {
        /** Settlement and original currencies match, the happy path: */
        if ($this->submittedAmountInOriginalCurrency === $this->submittedAmount) {
            return $this->processedAmount;
        }

        /** Currencies differ, but a full payment was made: */
        if ($this->processedAmount === $this->submittedAmount) {
            return $this->submittedAmountInOriginalCurrency;
        }

        /** Currencies differ, partial payment was made. Calculate back: */
        $rate = $this->submittedAmount / $this->submittedAmountInOriginalCurrency;

        return round($this->processedAmount / $rate, 2);
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
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
            return trim((string) $xml->mobilpay->error[0]);
        }

        return $this->action->label();
    }
}
