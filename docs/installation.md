# Netopia Module Installation

1. Add to your application via composer:
    ```bash
    composer require vanilo/netopia 
    ```
2. Add the module to `config/concord.php`:
    ```php
    <?php
    return [
        'modules' => [
             //...
             Vanilo\Netopia\Providers\ModuleServiceProvider::class
             //...
        ]
    ]; 
    ```

The following `.env` parameters must be set in order to work with this package.
These credentials can be obtained from the Netopia admin panel.

```dotenv
NETOPIA_SIGNATURE=XXXX-YYYYY-ZZZZ
NETOPIA_PUBLIC_CERTIFICATE_PATH=/home/test/public
NETOPIA_PRIVATE_CERTIFICATE_PATH=/home/test/private
NETOPIA_SANDBOX=true/false
```

## Registration with Payments Module

The module will register the payment gateway with the Vanilo Payments registry by default.

### Prevent from Auto-registration

If you don't want it to be registered automatically, you can prevent it by setting it in the module
configuration:

```php
//config/concord.php
return [
    'modules' => [
        //...
        Vanilo\Netopia\Providers\ModuleServiceProvider::class => [
            'gateway' => [
                'register' => false
            ]
        ]
        //...
    ]
]; 
```

### Manual Registration

If you disable registration and want to register the gateway manually you can do it by using the
Vanilo Payment module's payment gateway registry:

```php
use Vanilo\Netopia\NetopiaPaymentGateway;
use Vanilo\Payment\PaymentGateways;

PaymentGateways::register('gateway-id', NetopiaPaymentGateway::class);
```

In manual registration mode the class doesn't get bound with the Laravel DI container.
Therefore, you also have to do the binding manually:

```php
$this->app->bind(NetopiaPaymentGateway::class, function ($app) {
    return new NetopiaPaymentGateway(
        $this->config('signature'), // You can use different source than config
        $this->config('public_certificate_path'), // for these parameters
        $this->config('private_certificate_path'),
        $this->config('sandbox')
    );
});
```

---

**Next**: [Workflow &raquo;](workflow.md)
