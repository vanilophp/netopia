<?php

declare(strict_types=1);

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
     * The action attempted by Netopia (or MobilPay). The possible actions are below:
     * - [new, paid_pending, confirmed_pending, paid, confirmed, credit, canceled]
     * It's not the transaction status since actions can either fail or succeed
     */
    private string $action;

    public function __construct(
        string $paymentId,
        int $errorCode,
        float $processedAmount,
        string $errorMessage = null
    ) {
        $this->paymentId = $paymentId;
        $this->errorCode = $errorCode;
        $this->processedAmount = $processedAmount;
        $this->errorMessage = $errorMessage;
    }

    public function wasSuccessful(): bool
    {
        return 0 === $this->errorCode;
    }

    public function getMessage(): ?string
    {
        return $this->errorMessage;
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

    public function getReplyToNetopia(): Response
    {
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?><crc>Confirmation received with code {$this->errorCode}</crc>";

        return (new Response())->withHeaders(['Content-type' => 'application/xml'])->setContent($content);
    }
}
