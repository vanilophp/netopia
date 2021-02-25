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
use Vanilo\Netopia\Concerns\InteractsWithNetopia;
use Vanilo\Netopia\Factories\RequestFactory;
use Vanilo\Netopia\Factories\ResponseFactory;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;

class NetopiaPaymentGateway implements PaymentGateway
{
    use InteractsWithNetopia;

    public const DEFAULT_ID = 'netopia';

    public static function getName(): string
    {
        return 'Netopia';
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        return RequestFactory::create(
            $this->isSandbox,
            $this->signature,
            $this->publicCertificatePath,
            $payment,
            $options
        );
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        return ResponseFactory::create($request, $options, $this->privateCertificatePath);
    }

    public function isOffline(): bool
    {
        return false;
    }
}
