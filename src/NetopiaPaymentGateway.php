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
use Vanilo\Netopia\Concerns\HasFullNetopiaInteraction;
use Vanilo\Netopia\Factories\RequestFactory;
use Vanilo\Netopia\Factories\ResponseFactory;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\TransactionHandler;

class NetopiaPaymentGateway implements PaymentGateway
{
    use HasFullNetopiaInteraction;

    public const DEFAULT_ID = 'netopia';

    private static ?string $svg = null;

    private ?RequestFactory $requestFactory = null;

    public static function getName(): string
    {
        return 'Netopia';
    }

    public static function svgIcon(): string
    {
        return self::$svg ??= file_get_contents(__DIR__ . '/../../resources/logo.svg');
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory(
                $this->signature,
                $this->publicCertificatePath,
                $this->privateCertificatePath,
                $this->isSandbox,
                $this->returnUrl,
                $this->confirmUrl
            );
        }

        return $this->requestFactory->create($payment, $options);
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        return ResponseFactory::create($request, $this->privateCertificatePath);
    }

    public function transactionHandler(): ?TransactionHandler
    {
        return null;
    }

    public function isOffline(): bool
    {
        return false;
    }
}
