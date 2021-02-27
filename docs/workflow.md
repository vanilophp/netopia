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

### PaymentRequest

getHtmlSnippet() options:

- `autoRedirect`: bool, which if true, the rendered payment request from will automatically submit
  itself on load towards Netopia.

---

**Next**: [Examples &raquo;](examples.md)
