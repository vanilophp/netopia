# Netopia Payment Workflow

## Simple Gateway Usage

**Controller:**

```php
use Vanilo\Payment\PaymentGateways;

class OrderController
{
    public function submit(CreateOrderRequest $request)
    {
        $order = Order::createFrom($request);
        $gateway = PaymentGateways::make('netopia');
        $paymentRequest = $gateway->createPaymentRequest($order);
        
        return view('order.confirmation', [
            'order' => $order,
            'paymentRequest' => $paymentRequest
        ]);
    }
}
```

**Blade Template:**

```blade
{!! $paymentRequest->getHtmlSnippet(); !!}
```

## Options

### Request Factory

- `confirm_url`
- `return_url`
- `description`

---

**Next**: [Examples &raquo;](examples.md)
