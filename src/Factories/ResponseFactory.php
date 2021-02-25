<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Factories;

use Illuminate\Http\Request;
use SimpleXMLElement;
use Vanilo\Netopia\Exceptions\InvalidNetopiaKeyException;
use Vanilo\Netopia\Exceptions\MalformedNetopiaResponse;
use Vanilo\Netopia\Messages\NetopiaPaymentResponse;

class ResponseFactory
{
    public static function create(Request $request, array $options, string $privateCertificatePath): NetopiaPaymentResponse
    {
        $data = self::decrypt($request, $privateCertificatePath);
        $response = new NetopiaPaymentResponse();

        $response
            ->setProcessedAmount((float) $data->mobilpay->processed_amount[0])
            ->setAction($data->mobilpay->action[0])
            ->setPaymentId((string) $data->attributes()->id[0])
            ->setErrorCode((int) $data->mobilpay->error->attributes()->code[0]);

        return $response;
    }

    private static function decrypt(Request $request, string $privateCertificatePath): SimpleXMLElement
    {
        if (!$request->has('env_key') || !$request->has('data')) {
            throw MalformedNetopiaResponse::create();
        }

        $key = openssl_get_privatekey("file://{$privateCertificatePath}");
        if (false === $key) {
            throw InvalidNetopiaKeyException::fromPath($privateCertificatePath);
        }

        $srcData = base64_decode($request->get('data'));
        $srcEnvKey = base64_decode($request->get('env_key'));
        $data = null;
        openssl_open($srcData, $data, $srcEnvKey, $key, 'RC4');

        return simplexml_load_string($data);
    }
}
