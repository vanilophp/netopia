<?php

namespace Vanilo\Netopia\Tests\Gateway;

use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Netopia\Tests\TestCase;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\PaymentGateways;

class RegistrationWithCustomIdTest extends TestCase
{
    protected function setUp(): void
    {
        PaymentGateways::reset();
        parent::setUp();
    }

    /** @test */
    public function the_gateway_id_can_be_changed_from_within_the_configuration()
    {
        $this->assertCount(2, PaymentGateways::ids());
        $this->assertContains('awesomegateway', PaymentGateways::ids());
    }

    /** @test */
    public function the_gateway_can_be_instantiated()
    {
        $netopiaGateway = PaymentGateways::make('awesomegateway');

        $this->assertInstanceOf(PaymentGateway::class, $netopiaGateway);
        $this->assertInstanceOf(NetopiaPaymentGateway::class, $netopiaGateway);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        config(['vanilo.netopia.gateway.id' => 'awesomegateway']);
    }
}
