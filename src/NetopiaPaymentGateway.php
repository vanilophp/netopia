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

    private ?RequestFactory $requestFactory = null;

    public static function getName(): string
    {
        return 'Netopia';
    }

    public static function svgIcon(): string
    {
        return '<svg x="0" y="0" viewBox="0 0 144 144" xml:space="preserve" width="144" height="144"><style type="text/css">.st0{fill:#fff}</style><path fill="#030614" fill-opacity="1" stroke="none" stroke-width="1.512" stroke-miterlimit="4" stroke-dasharray="1.512 1.512" stroke-dashoffset="0" d="M0 0h144v144H0z"/><path class="st0" d="M5.8 85.475h5.8c1-.1 2 .2 2.8.9.7.7 1 1.6.9 2.6.1 1-.3 2-.9 2.7-.9.7-2 1.1-3.2 1h-2v4.2H5.8zm3.5 4.9h.9c.5 0 1-.1 1.4-.4.3-.2.4-.6.4-.9 0-.3-.1-.7-.3-.9-.4-.3-.9-.4-1.3-.4h-1z" /><path class="st0" d="M28.5 94.975h-4l-.5 1.9h-3.6l4.3-11.3h3.8l4.3 11.3H29zm-.8-2.5l-1.3-4.1-1.2 4.1z" /><path class="st0" d="M36.9 85.475h3.9l2.3 3.9 2.3-3.8h3.8l-4.4 6.6v4.7h-3.5v-4.7z" /><path class="st0" d="M55.9 85.475h4.6l1.8 6.9 1.8-6.9h4.6v11.3h-2.9v-8.7l-2.2 8.7H61l-2.2-8.7v8.7h-2.9v-11.3z" /><path class="st0" d="M76.4 85.475h9.5v2.4H80v1.8h5.5v2.3H80v2.3h6v2.6h-9.6z" /><path class="st0" d="M93.4 85.475h3.3l4.3 6.3v-6.3h3.3v11.3H101l-4.3-6.2v6.2h-3.3v-11.3z" /><path class="st0" d="M111.4 85.475H122v2.8h-3.5v8.5h-3.6v-8.5h-3.6z" /><path class="st0" d="M128.5 93.075l3.3-.2c0 .4.2.9.4 1.2.4.5 1 .7 1.6.7.4 0 .8-.1 1.2-.4.2-.2.4-.5.4-.8 0-.3-.2-.6-.4-.8-.5-.3-1.2-.6-1.8-.6-1.2-.2-2.3-.7-3.3-1.4-.7-.5-1-1.3-1-2.2 0-.6.2-1.2.5-1.7.4-.6.9-1 1.6-1.2.9-.3 1.8-.5 2.8-.4 1.2-.1 2.3.2 3.3.8.8.6 1.3 1.6 1.4 2.6l-3.3.2c0-.4-.2-.8-.6-1.1-.3-.3-.7-.4-1.2-.3-.3 0-.6.1-.9.3-.2.2-.3.4-.3.6 0 .2.1.4.3.5.4.2.8.4 1.2.4 1.1.2 2.2.5 3.3 1 .6.3 1.1.7 1.5 1.3.3.5.5 1.1.5 1.7 0 .7-.2 1.4-.6 2-.4.6-1 1.1-1.7 1.4-.9.3-1.8.5-2.7.5-1.4.2-2.9-.2-4-1.1-.9-.9-1.4-1.9-1.5-3z" /><path class="st0" transform="translate(5 46.275)" d="M10.8 27.6V1.5H7.7v3.7L3 1.5H0v26.1h3.2V5.4l4.5 3.3v18.9z"/><path class="st0" transform="translate(5 46.275)" d="M18.3 8l1.1 2.6h1.1v17h7.8v-2.9h-4.8V10.8h5.7l-1.3-3h-4.5V4.3h4.8V1.5h-7.7V8z"/><path class="st0" transform="translate(5 46.275)" d="M37 4.6h4v23h3.1v-23h3.5V1.5H37z"/><path class="st0" d="M115.2 47.775h3.2v26.1h-3.2z"/><path class="st0" d="M133.4 47.775c-4.6 0-4.9 3.5-4.9 3.5v2.9h-2l1 3h1v16.8h3.2v-16.9h3.8v16.8h2.9v-22.7c-.1 0-.4-3.4-5-3.4zm2.1 6.2h-3.9v-2.2s-.2-1.3 1.6-1.3 2.2 1.1 2.2 1.1z" /><path class="st0" d="M72.1 46.775c-7.8-.1-14.1 6.1-14.2 13.9 0 7.8 6.4 14.2 14.2 14.2 7.8 0 14.2-6.4 14.2-14.2-.1-7.8-6.5-14-14.2-13.9zm0 24.6c-6 .1-10.9-4.7-11-10.7.8-6.1 6.3-10.4 12.3-9.6 5.1.6 9 4.6 9.6 9.6 0 6-4.9 10.8-10.9 10.7z" /><path class="st0" d="M97.2 47.775h-3v26.1h3.3v-4.7s10.2-.6 10.2-10.4-10.5-11-10.5-11zm.1 18.8v-15.6c1.3 0 7.6 1.3 7.6 7.9 0 6.6-7.6 7.7-7.6 7.7z" /></svg>';
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
