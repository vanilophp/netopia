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
        $xmlResponse = self::decrypt($request, $privateCertificatePath);

        return new NetopiaPaymentResponse(
            (string) $xmlResponse->attributes()->id[0],
            (int) $xmlResponse->mobilpay->error->attributes()->code[0],
            (float) $xmlResponse->mobilpay->processed_amount[0],
            isset($xmlResponse->mobilpay->error[0]) ? (string) $xmlResponse->mobilpay->error[0] : null,
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
        if (!openssl_open($encryptedData, $xmlResponse, $envelopeKey, $privateKey, 'RC4')) {
            throw new NetopiaDecryptionException();
        }

        return simplexml_load_string($xmlResponse);
    }
}
