<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Tests;

use Vanilo\Netopia\Factories\RequestFactory;
use Vanilo\Netopia\Messages\NetopiaPaymentRequest;
use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Netopia\Tests\Dummies\Order;
use Vanilo\Netopia\Tests\TestCase;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;

class RequestFactoryTest extends TestCase
{
    /** @test */
    public function it_creates_a_request_object()
    {
        $method = PaymentMethod::create([
            'gateway' => NetopiaPaymentGateway::getName(),
            'name' => 'Netopia',
        ]);

        $order = Order::create(['currency' => 'RON', 'amount' => 19.99]);

        $payment = PaymentFactory::createFromPayable($order, $method);

        $request = RequestFactory::create(true, 'test', '/home/test', $payment);

        $this->assertInstanceOf(
            NetopiaPaymentRequest::class,
            $request
        );
    }
}
