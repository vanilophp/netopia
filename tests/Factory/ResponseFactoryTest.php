<?php

namespace Vanilo\Netopia\Tests\Factory;

use Illuminate\Http\Request;
use Vanilo\Netopia\Factories\ResponseFactory;
use Vanilo\Netopia\Messages\NetopiaPaymentResponse;
use Vanilo\Netopia\Tests\TestCase;

class ResponseFactoryTest extends TestCase
{
    /** @test */
    public function it_creates_a_response_object()
    {
        $responseData = $this->getEncryptedRespose();

        $request = new Request($responseData);

        $response = ResponseFactory::create($request, [], __DIR__ . '/../keys/server.key');

        $this->assertInstanceOf(
            NetopiaPaymentResponse::class,
            $response
        );

        $this->assertEquals(22, $response->getAmountPaid());
    }

    private function getEncryptedRespose()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?><order type="card" id="string64" timestamp="YYYYMMDDHHMMSS">{your_request_XML}<mobilpay timestamp="YYYYMMDDHHMMSS" crc="XXXXX"><action>action_type</action><customer type="person|company"><first_name>first_name</first_name><last_name>last_name</last_name><address>address</address><email>email_address</email><mobile_phone>phone_no</mobile_phone></customer><purchase>mobilPay_purchase_no</purchase><original_amount>XX.XX</original_amount><processed_amount>22</processed_amount><pan_masked>X****YYYY</pan_masked><payment_instrument_id>ZZZZZZZ</payment_instrument_id><token_id>token_identifier</token_id><token_expiration_date>YYYY-MM-DD HH:MM:SS</token_expiration_date><error code="N">error_message</error></mobilpay></order>';

        $publicKey = openssl_pkey_get_public(file_get_contents(__DIR__ . '/../keys/server.crt'));
        $encData   = null;
        $envKeys   = null;
        $publicKey = array($publicKey);

        openssl_seal($xml, $encData, $envKeys, $publicKey, 'RC4');

        return [
            'data'    => base64_encode($encData),
            'env_key' => base64_encode($envKeys[0])
        ];
    }
}
