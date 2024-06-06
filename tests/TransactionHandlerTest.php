<?php

declare(strict_types=1);

/**
 * Contains the TransactionHandlerTest class.
 *
 * @copyright   Copyright (c) 2024 Vanilo UG
 * @author      Attila Fulop
 * @license     MIT
 * @since       2024-06-06
 *
 */

namespace Vanilo\Netopia\Tests;

use Vanilo\Netopia\Messages\NetopiaPaymentRequest;
use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Netopia\Tests\Dummies\Order;
use Vanilo\Netopia\Transaction\Handler;
use Vanilo\Payment\Contracts\TransactionNotCreated;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;
use Vanilo\Payment\Models\PaymentStatus;
use Vanilo\Payment\PaymentGateways;

class TransactionHandlerTest extends TestCase
{
    private PaymentMethod $method;

    protected function setUp(): void
    {
        parent::setUp();

        $this->method = PaymentMethod::create([
            'gateway' => NetopiaPaymentGateway::DEFAULT_ID,
            'name' => 'Netopia',
        ]);
    }

    /** @test */
    public function it_returns_a_netopia_handler_instance()
    {
        $this->assertInstanceOf(Handler::class, PaymentGateways::make('netopia')->transactionHandler());
    }

    /** @test */
    public function it_returns_no_transaction_when_the_payment_is_pending()
    {
        $order = Order::create(['currency' => 'RON', 'amount' => 315]);
        $payment = PaymentFactory::createFromPayable($order, $this->method);

        $this->assertInstanceOf(
            TransactionNotCreated::class,
            PaymentGateways::make('netopia')->transactionHandler()->getRetryRequest($payment),
        );
    }

    /** @test */
    public function it_returns_a_payment_request_when_the_payment_is_declined()
    {
        $order = Order::create(['currency' => 'RON', 'amount' => 180]);
        $payment = PaymentFactory::createFromPayable($order, $this->method);
        $payment->status = PaymentStatus::DECLINED;
        $payment->save();

        $this->assertInstanceOf(
            NetopiaPaymentRequest::class,
            PaymentGateways::make('netopia')->transactionHandler()->getRetryRequest($payment),
        );
    }

    private function gateway(): NetopiaPaymentGateway
    {
        return PaymentGateways::make('netopia');
    }
}
