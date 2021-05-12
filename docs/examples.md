# Examples

The Example below shows parts of the code that you can put in your application.

### CheckoutController

The controller below processes a submitted checkout, prepares the payment and returns the thank you
page with the prepared payment request:

```php
use Vanilo\Framework\Models\Order;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;
use Vanilo\Payment\PaymentGateways;

class CheckoutController
{
    public function store(Request $request)
    {
        $order = Order::createFrom($request);
        $paymentMethod = PaymentMethod::find($request->get('paymentMethod'));
        $payment = PaymentFactory::createFromPayable($order, $paymentMethod);
        $gateway = PaymentGateways::make('netopia');
        $paymentRequest = $gateway->createPaymentRequest($payment);
        
        return view('checkout.thank-you', [
            'order' => $order,
            'paymentRequest' => $paymentRequest
        ]);
    }
}
```

### checkout/thank-you.blade.php

This sample blade template contains a thank you page where you can render the payment initiation
form:

**Blade Template:**

```blade
@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Thank you</h1>

        <div class="alert alert-success">Your order has been registered with number
            <strong>{{ $order->getNumber() }}</strong>.
        </div>

        <h3>Payment</h3>

        {!! $paymentRequest->getHtmlSnippet(); !!}
    </div>
@endsection
```

### NetopiaReturnController

```php
class NetopiaReturnController extends Controller
{
    // This action renders a view for the Consumer after return from Netopia.
    // Before this action, confirm has also been called by Netopia already
    // in the background, therefore the payment status shall be updated
    public function return(Request $request)
    {
        $payment = Payment::findByPaymentId($request->get('orderId'));

        return view('payment.return_netopia', [ // The view is from your application
            'payment' => $payment,
            'order'   => $payment->getPayable(),
        ]);
    }

    // This is the action that Netopia calls in the background if it
    // wants to communicate the status of a payment attempt to us
    // it gets called via POST and is a server-to-server call.
    public function confirm(Request $request)
    {
        $response = PaymentGateways::make('netopia')->processPaymentResponse($request);
        $payment  = Payment::findByPaymentId($response->getPaymentId());

        if (!$payment) {
            // This returns an HTTP response in the format that Netopia understands
            return new ErrorResponseToNetopia(404, 'Could not locate payment with id ' . $response->getPaymentId());
        }

        $handler = new PaymentResponseHandler($payment, $response);
        $handler->writeResponseToHistory();
        $handler->updatePayment();
        $handler->fireEvents();

        // Returns a success response in the format Netopia expects
        return new SuccessResponseToNetopia();
    }
}
```

### Routes

The routes for Netopia should look like:

```php
//web.php
Route::group(['prefix' => 'payment/netopia', 'as' => 'payment.netopia.'], function() {
    Route::post('confirm', 'NetopiaReturnController@confirm')->name('confirm');
    Route::get('return', 'NetopiaReturnController@return')->name('return');
});
```

**IMPORTANT!**: Make sure to **disable CSRF verification** for these URLs, by adding them as
exceptions to `app/Http/Middleware/VerifyCsrfToken`:

```php
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/payment/netopia/*'
    ];
}
```

Have fun!

---
Congrats, you've reached the end of this doc! 🎉
