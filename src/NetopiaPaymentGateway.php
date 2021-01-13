<?php
/**
 * Contains the NetopiaPaymentGateway class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-01-13
 *
 */

namespace Vanilo\Netopia;

use Illuminate\Http\Request;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;

class NetopiaPaymentGateway implements PaymentGateway
{
    public const DEFAULT_ID = 'netopia';

    private string $uniqueKey;

    public function __construct(string $uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;
    }

    public static function getName(): string
    {
        return 'Netopia';
    }

    public function createPaymentRequest(
        Payment $payment,
        Address $shippingAddress = null,
        array $options = []
    ): PaymentRequest {
        // todo
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        // todo
    }

    public function isOffline(): bool
    {
        return false;
    }
}
