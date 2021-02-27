<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Messages;

use SimpleXMLElement;
use Vanilo\Payment\Contracts\PaymentResponse;

class NetopiaPaymentResponse implements PaymentResponse
{
    /**
     * The error code states whether the action has been successful or not:
     *  - 0 (zero) => the action has succeeded
     *  - Non-zero => the action has failed
     */
    private int $errorCode;

    private string $message;

    private string $paymentId;

    private float $processedAmount;

    /**
     * The action attempted by Netopia (or MobilPay). The possible actions are below:
     * - [new, paid_pending, confirmed_pending, paid, confirmed, credit, canceled]
     * It's not the transaction status since actions can either fail or succeed
     */
    private string $action;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->paymentId = (string) $xml->attributes()->id[0];
        $this->errorCode = (int) $xml->mobilpay->error->attributes()->code[0];
        $this->processedAmount = (float) $xml->mobilpay->processed_amount[0];
        $this->message = isset($xml->mobilpay->error[0]) ? (string) $xml->mobilpay->error[0] : '';
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
        return $this->paymentId;
    }

    public function getAmountPaid(): ?float
    {
        return $this->processedAmount;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }
}
