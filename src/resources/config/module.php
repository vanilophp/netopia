<?php

use Vanilo\Netopia\NetopiaPaymentGateway;

return [
    'gateway' => [
        'register' => true,
        'id'       => NetopiaPaymentGateway::DEFAULT_ID
    ],
    'unique_key'    => env('NETOPIA_UNIQUE_KEY'),
];
