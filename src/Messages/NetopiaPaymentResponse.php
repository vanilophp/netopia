<?php

namespace Vanilo\Netopia\Messages;

use Illuminate\Http\Response;
use Vanilo\Payment\Contracts\PaymentResponse;

class NetopiaPaymentResponse implements PaymentResponse
{
    /**
     * The error code states whether the action has been successful or not.A 0 (zero) value states that the action has
     * succeeded. A different value means it has not.
     */
    private int $errorCode;

    private ?string $errorMessage;

    private string $paymentId;

    private float $processedAmount;

    /**
     * The action   attempted   by   mobilPay.   Possible   actions   are   “new,paid_pending, confirmed_pending, paid,
     * confirmed, credit, canceled”. This is not the status of the transaction, as all actions can either fail or
     * succeed.
     */
    private string $action;

    public function wasSuccessful(): bool
    {
        return $this->errorCode == 0;
    }

    public function getMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getTransactionId(): ?string
    {
        die();
    }

    public function getAmountPaid(): ?float
    {
        return $this->processedAmount;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): NetopiaPaymentResponse
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function setErrorCode(int $errorCode): NetopiaPaymentResponse
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function setErrorMessage(?string $errorMessage): NetopiaPaymentResponse
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function setProcessedAmount(float $processedAmount): NetopiaPaymentResponse
    {
        $this->processedAmount = $processedAmount;

        return $this;
    }

    public function setAction(string $action): NetopiaPaymentResponse
    {
        $this->action = $action;

        return $this;
    }

    public function getReplyToNetopia(): Response
    {
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?><crc>Confirmation received with code {$this->errorCode}</crc>";

        return (new Response())->withHeaders(['Content-type' => 'application/xml'])->setContent($content);
    }
}
