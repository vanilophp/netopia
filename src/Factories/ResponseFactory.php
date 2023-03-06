<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Factories;

use Illuminate\Http\Request;
use SimpleXMLElement;
use Vanilo\Netopia\Exceptions\InvalidNetopiaKeyException;
use Vanilo\Netopia\Exceptions\MalformedNetopiaResponse;
use Vanilo\Netopia\Exceptions\NetopiaDecryptionException;
use Vanilo\Netopia\Messages\NetopiaPaymentResponse;

final class ResponseFactory
{
    public static function create(Request $request, string $privateCertificatePath): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            self::decrypt($request, $privateCertificatePath)
        );
    }

    private static function decrypt(Request $request, string $privateCertificatePath): SimpleXMLElement
    {
        if (!$request->has('env_key') || !$request->has('data')) {
            throw MalformedNetopiaResponse::create();
        }

        $privateKey = openssl_get_privatekey("file://{$privateCertificatePath}");
        if (false === $privateKey) {
            throw InvalidNetopiaKeyException::fromPath($privateCertificatePath);
        }

        $encryptedData = base64_decode($request->get('data'));
        $envelopeKey = base64_decode($request->get('env_key'));
        $xmlResponse = null;
        // @see https://github.com/mobilpay/composer/issues/6
        if (!openssl_open($encryptedData, $xmlResponse, $envelopeKey, $privateKey, 'RC4')) {
            throw new NetopiaDecryptionException('Failed to decrypt the message');
        }

        $result = simplexml_load_string($xmlResponse);
        if (false === $result) {
            throw new NetopiaDecryptionException('Failed parsing the decrypted response XML');
        }

        return $result;
    }
}
