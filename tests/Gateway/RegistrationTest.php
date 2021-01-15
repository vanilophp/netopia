<?php

namespace Vanilo\Netopia\Tests\Gateway;

use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Netopia\Tests\TestCase;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\PaymentGateways;

class RegistrationTest extends TestCase
{
    /** @test */
    public function the_gateway_is_registered_out_of_the_box_with_defaults()
    {
        $this->assertCount(2, PaymentGateways::ids());
        $this->assertContains(NetopiaPaymentGateway::DEFAULT_ID, PaymentGateways::ids());
    }

    /** @test */
    public function the_gateway_can_be_instantiated()
    {
        $netopiaGateway = PaymentGateways::make('netopia');

        $this->assertInstanceOf(PaymentGateway::class, $netopiaGateway);
        $this->assertInstanceOf(NetopiaPaymentGateway::class, $netopiaGateway);
    }
}
