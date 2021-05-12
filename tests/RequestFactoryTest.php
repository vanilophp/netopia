<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Tests;

use ReflectionClass;
use ReflectionProperty;
use Vanilo\Netopia\Factories\RequestFactory;
use Vanilo\Netopia\Messages\NetopiaPaymentRequest;
use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Netopia\Tests\Dummies\Order;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;

class RequestFactoryTest extends TestCase
{
    private PaymentMethod $method;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->view->addNamespace('netopia-tests', __DIR__ . '/views');

        $this->method = PaymentMethod::create([
            'gateway' => NetopiaPaymentGateway::getName(),
            'name' => 'Netopia',
        ]);
    }

    /** @test */
    public function it_creates_a_request_object()
    {
        $order = Order::create(['currency' => 'RON', 'amount' => 19.99]);
        $payment = PaymentFactory::createFromPayable($order, $this->method);
        $factory = $this->createTestFactory();
        $request = $factory->create($payment);

        $this->assertInstanceOf(
            NetopiaPaymentRequest::class,
            $request
        );
    }

    /** @test */
    public function it_renders_the_default_html_snippet_and_it_looks_good()
    {
        $order = Order::create(['currency' => 'USD', 'amount' => 27.69]);
        $payment = PaymentFactory::createFromPayable($order, $this->method);
        $html = $this->createTestFactory()
                     ->create($payment)
                     ->getHtmlSnippet();

        $this->assertStringContainsString('<form method="post', $html);
        $this->assertStringContainsString('action="https://sandboxsecure.mobilpay.ro"', $html);
        $this->assertStringContainsString('<input type="hidden" name="env_key"', $html);
        $this->assertStringContainsString('<input type="hidden" name="data"', $html);
        $this->assertStringContainsString('</form>', $html);
    }

    /** @test */
    public function a_custom_view_can_be_passed_as_an_option()
    {
        $order = Order::create(['currency' => 'EUR', 'amount' => 169.90]);
        $payment = PaymentFactory::createFromPayable($order, $this->method);
        $html = $this->createTestFactory()
                     ->create($payment, ['view' => 'netopia-tests::_custom_form'])
                     ->getHtmlSnippet();

        $this->assertStringContainsString('<!-- Hey I am very unique !-->', $html);
        $this->assertStringContainsString('<form method="POST', $html);
        $this->assertStringContainsString('action="https://sandboxsecure.mobilpay.ro"', $html);
        $this->assertStringContainsString('<input type="hidden" name="env_key"', $html);
        $this->assertStringContainsString('<input type="hidden" name="data"', $html);
    }

    /** @test */
    public function it_converts_return_and_confirm_urls_to_full_urls_if_only_paths_are_specified()
    {
        $order = Order::create(['currency' => 'RON', 'amount' => 19.99]);
        $payment = PaymentFactory::createFromPayable($order, $this->method);
        $factory = new RequestFactory(
            'test-signature',
            __DIR__ . '/keys/server.crt',
            __DIR__ . '/keys/server.key',
            true,
            '/netopia/return',
            '/netopia/confirm'
        );
        $request = $factory->create($payment);

        $returnUrl = $this->getPrivateProperty(NetopiaPaymentRequest::class, 'returnUrl');
        $this->assertStringStartsWith('http', $returnUrl->getValue($request));
        $this->assertStringEndsWith('/netopia/return', $returnUrl->getValue($request));

        $confirmUrl = $this->getPrivateProperty(NetopiaPaymentRequest::class, 'confirmUrl');
        $this->assertStringStartsWith('http', $confirmUrl->getValue($request));
        $this->assertStringEndsWith('/netopia/confirm', $confirmUrl->getValue($request));
    }

    private function createTestFactory(): RequestFactory
    {
        return new RequestFactory(
            'test-signature',
            __DIR__ . '/keys/server.crt',
            __DIR__ . '/keys/server.key',
            true,
            'https://return.url',
            'https://confirm.url'
        );
    }

    private function getPrivateProperty(string $className, string $propertyName): ReflectionProperty
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
