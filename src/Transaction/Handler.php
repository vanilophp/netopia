<?php

declare(strict_types=1);

/**
 * Contains the Handler class.
 *
 * @copyright   Copyright (c) 2024 Vanilo UG
 * @author      Attila Fulop
 * @license     MIT
 * @since       2024-06-06
 *
 */

namespace Vanilo\Netopia\Transaction;

use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\Transaction;
use Vanilo\Payment\Contracts\TransactionHandler;
use Vanilo\Payment\Contracts\TransactionNotCreated;
use Vanilo\Payment\Responses\NoTransaction;

class Handler implements TransactionHandler
{
    public function __construct(
        private NetopiaPaymentGateway $gateway,
    ) {
    }

    public function supportsRefunds(): bool
    {
        return false; // In, fact it does via the undocumented SOAP API
    }

    public function supportsRetry(): bool
    {
        return true;
    }

    public function allowsRefund(Payment $payment): bool
    {
        return false;
    }

    public function issueRefund(Payment $payment, float $amount, array $options = []): Transaction|TransactionNotCreated
    {
        return NoTransaction::create($payment, 'Feature not implemented');
    }

    public function canBeRetried(Payment $payment): bool
    {
        return $payment->getStatus()->isDeclined();
    }

    public function getRetryRequest(Payment $payment, array $options = []): PaymentRequest|TransactionNotCreated
    {
        if (!$this->canBeRetried($payment)) {
            return NoTransaction::create($payment, __('The payment is not in a state that allows retrying'));
        }

        return $this->gateway->createPaymentRequest($payment, null, $options);
    }
}
