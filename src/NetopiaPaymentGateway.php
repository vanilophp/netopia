<?php

declare(strict_types=1);
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
use Vanilo\Netopia\Concerns\HasCallbackUrls;
use Vanilo\Netopia\Concerns\HasNetopiaConfig;
use Vanilo\Netopia\Factories\RequestFactory;
use Vanilo\Netopia\Factories\ResponseFactory;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;

class NetopiaPaymentGateway implements PaymentGateway
{
    use HasNetopiaConfig;
    use HasCallbackUrls;

    public const DEFAULT_ID = 'netopia';

    private ?RequestFactory $requestFactory = null;

    public function __construct(
        string $signature,
        string $publicCertificatePath,
        string $privateCertificatePath,
        bool $isSandbox,
        string $returnUrl,
        string $confirmUrl
    )
    {
        $this->signature = $signature;
        $this->publicCertificatePath = $publicCertificatePath;
        $this->privateCertificatePath = $privateCertificatePath;
        $this->isSandbox = $isSandbox;
        $this->returnUrl = $returnUrl;
        $this->confirmUrl = $confirmUrl;
    }

    public static function getName(): string
    {
        return 'Netopia';
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory($this->signature, $this->publicCertificatePath, $this->isSandbox);
        }

        return $this->requestFactory->create($payment, $options);
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        return ResponseFactory::create($request, $this->privateCertificatePath);
    }

    public function isOffline(): bool
    {
        return false;
    }
}
