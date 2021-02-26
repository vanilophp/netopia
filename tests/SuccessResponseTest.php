<?php

declare(strict_types=1);

/**
 * Contains the SuccessResponseTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-26
 *
 */

namespace Vanilo\Netopia\Tests;

use Illuminate\Http\Request;
use Vanilo\Netopia\Http\Responses\SuccessResponseToNetopia;
use Vanilo\Payment\PaymentGateways;

class SuccessResponseTest extends TestCase
{
    /** @test */
    public function it_can_return_a_netopia_compliant_success_message()
    {
        $xml = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<order type="card" id="string64" timestamp="YYYYMMDDHHMMSS">
    <signature>CLTN-UA6F-T67W-5BPB-CH82</signature>
    <invoice currency="RON" amount="XX.YY" installments="R1,R2"
        selected_installments="R2" customer_type="2"
        customer_id="internal_id" token_id="token_identifier"
        pan_masked="NNNN">
        <details>Payment Details</details>
        <contact_info>
            <billing type="company|person">
                <first_name>first_name</first_name>
                <last_name>last_name</last_name>
                <email>email_address</email>
                <address>address</address>
                <mobile_phone>mobile_phone</mobile_phone>
            </billing>
        </contact_info>
    </invoice>
    <params>
        <param>
            <name>param1Name</name>
            <value>param1Value</value>
        </param>
    </params>
    <url>
        <confirm>http://www.your_website.com/confirm</confirm>
        <return>http://www.your_website.com/return</return>
    </url>

<mobilpay timestamp="YYYYMMDDHHMMSS" crc="XXXXX">
    <action>action_type</action>
    <customer type="person|company">
    <first_name>first_name</first_name>
    <last_name>last_name</last_name>
    <address>address</address>
    <email>email_address</email>
    <mobile_phone>phone_no</mobile_phone>
    </customer>
    <purchase>mobilPay_purchase_no</purchase>
    <original_amount>XX.XX</original_amount>
    <processed_amount>NN.NN</processed_amount>
    <pan_masked>X****YYYY</pan_masked>
    <payment_instrument_id>ZZZZZZZ</payment_instrument_id>
    <token_id>token_identifier</token_id>
    <token_expiration_date>YYYY-MM-DD HH:MM:SS</token_expiration_date>
    <error code="N">error_message</error>
</mobilpay>
</order>
EOT;

        $cert = file_get_contents(__DIR__ . '/keys/server.crt');
        $pk1 = openssl_get_publickey($cert);
        openssl_seal($xml, $sealed, $ekeys, [$pk1], 'RC4');

        $payload = ['env_key' => base64_encode($ekeys[0]), 'data' => base64_encode($sealed)];
        $this->post('/confirm', $payload)
            ->assertSee('Roger that')
            ->assertStatus(200)
            ->assertHeader('Content-type', 'application/xml; charset="utf-8"');
    }

    protected function defineRoutes($router)
    {
        $router->post('/confirm', function (Request $request) {
            $netopiaResponse = PaymentGateways::make('netopia')->processPaymentResponse($request);

            return new SuccessResponseToNetopia('Roger that');
        });

        $router->get('/throw-netopia-key-error', function () {
            throw InvalidNetopiaKeyException::fromPath('/some/path/server.key');
        });
    }
}
