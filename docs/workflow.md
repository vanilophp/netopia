# Netopia Payment Workflow

The typical Vanilo Payments workflow with Netopia consists of the following steps:

1. Create an **Order** (or any ["Payable"](https://vanilo.io/docs/2.x/payments#payables))
2. Obtain the **payment method** from the checkout<sup>*</sup>
3. Get the appropriate **gateway instance** associated with the payment method
4. Generate a **payment request** using the gateway
5. Inject the **HTML snippet** on the checkout/thankyou page
6. The HTML snippet **redirects** the Consumer to the **processor's payment page**
7. The payment processor sends a **callback** to your site with the payment result (to `confirm_url`)
8. The consumer gets **redirected back** to your site (to `return_url`)

> *: If your site's one and only payment method is Card/Netopia (ie. no cash on delivery, etc), then this step is not needed of course.

## Obtain Gateway Instance

Once you have an order (or any other payable), then the starting point of payment operations is
obtaining a gateway instance:

```php
\Vanilo\Payment\PaymentGateways::make('netopia');
```

The gateway provides you two essential methods:

- `createPaymentRequest` - Assembles the payment initiation request from an order that can be injected on your checkout page.
- `processPaymentResponse` - Processes the HTTP response returning from Netopia after a payment attempt.

## Starting Online Payments

**Controller:**

```php
use Vanilo\Framework\Models\Order;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;
use Vanilo\Payment\PaymentGateways;

class OrderController
{
    public function submit(Request $request)
    {
        $order = Order::createFrom($request);
        $paymentMethod = PaymentMethod::find($request->get('paymentMethod'));
        $payment = PaymentFactory::createFromPayable($order, $paymentMethod);
        $gateway = PaymentGateways::make('netopia');
        $paymentRequest = $gateway->createPaymentRequest($payment);
        
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

The generated HTML snippet will contain a prepared, encrypted HTML Form with all the necessary
details that can be submitted to Netopia to start the payment process.

### Payment Request Options

The gateway's `createPaymentRequest` method accepts additional parameters that can be used to
customize the generated request.

The signature of the method is the following:

```php
public function createPaymentRequest(
    Payment $payment,
    Address $shippingAddress = null,
    array $options = []
    ): PaymentRequest
```

1. The first parameter is the payment. Every attempt to settle a payable is a new `Payment` record.
2. The second one is the shipping address in case it differs from billing address. Netopia doesn't support this option, leave it always `NULL`.
3. The third parameters is an array with possible options.

You can pass the following values in the `$option` array:

| Array Key     | Example                                | Description                                                                                                                                      |
|:--------------|:---------------------------------------|:-------------------------------------------------------------------------------------------------------------------------------------------------|
| `confirm_url` | https://your.site/netopia/confirmation | A custom confirmation callback URL (of your site) that will be used by Netopia for this concrete single payment.                                 |
| `return_url`  | https://your.site/purchase/complete    | A custom return URL (of your site) where Netopia will redirect the consumer for this single payment after the payment process has completed.     |
| `description` | Order with number XYZ                  | By default it's "Order no. XXX", but you can pass any value here that will be visible on Netopia as description.                                 |
| `view`       | `payment.netopia._form`              | By default it's `netopia::_request` You can use a custom blade view to render the HTML snippet instead of the default one this library provides. |

**Example**:

```php
$options = [
    'confirm_url' => 'https://your.site/netopia/confirmation',
    'return_url' => 'https://your.site/purchase/complete',
    'description' => 'Order with number ' . $order->number,
    'view'=> 'payment.netopia._form',
];
$gateway->createPaymentRequest($payment, null, $options);
```

#### Customizing The View

Apart from passing the `view` option to the `createPaymentRequest`, there's an even more simple way:
Laravel lets you customize the views from vendor packages like this [...]


### PaymentRequest

getHtmlSnippet() options:

- `autoRedirect`: bool, which if true, the rendered payment request from will automatically submit
  itself on load towards Netopia.

## Confirm And Return URLs

Netopia uses two URLs on your site during the payment process the confirm and the return URLS.
Although you can set these URLs directly in the Netopia Admin panel, typically people don't do it,
but send these URLs along with each payment request. You can leave the values empty in the Netopia
Admin panel.

### The Confirm URL

This is a URL in your web application that will be called (using `POST` method) whenever the status
of a payment changes or a manual IPN is being sent. This is a transparent asynchronous call,
however, the first call is always synchronous;

### The Return URL

This is a URL in your web application where the client will be redirected (using `GET` method) to
once the payment is complete. Not to be confused with a success or cancel URL, the information
displayed here is dynamic, based on the information previously sent to confirm URL.

---

**Next**: [Examples &raquo;](examples.md)
