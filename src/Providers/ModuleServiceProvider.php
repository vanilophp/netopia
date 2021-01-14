<?php
/**
 * Contains the ModuleServiceProvider class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-01-13
 *
 */

namespace Vanilo\Netopia\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Payment\PaymentGateways;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    public function boot()
    {
        parent::boot();

        if ($this->config('gateway.register', true)) {
            PaymentGateways::register(
                $this->config('gateway.id', NetopiaPaymentGateway::DEFAULT_ID),
                NetopiaPaymentGateway::class
            );
        }

        $this->app->bind(NetopiaPaymentGateway::class, function ($app) {
            return new NetopiaPaymentGateway(
                $this->config('signature'),
                $this->config('public_certificate_path'),
                $this->config('private_certificate_path'),
                $this->config('sandbox')
            );
        });
    }
}
