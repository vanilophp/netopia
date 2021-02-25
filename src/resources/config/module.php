<?php

declare(strict_types=1);

use Vanilo\Netopia\NetopiaPaymentGateway;

return [
    'gateway' => [
        'register' => true,
        'id' => NetopiaPaymentGateway::DEFAULT_ID
    ],
    'signature' => env('NETOPIA_SIGNATURE'),
    'public_certificate_path' => env('NETOPIA_PUBLIC_CERTIFICATE_PATH'),
    'private_certificate_path' => env('NETOPIA_PRIVATE_CERTIFICATE_PATH'),
    'sandbox' => (bool) env('NETOPIA_SANDBOX', false),
    'return_url' => env('NETOPIA_RETURN_URL', ''),
    'confirm_url' => env('NETOPIA_CONFIRM_URL', ''),
];
