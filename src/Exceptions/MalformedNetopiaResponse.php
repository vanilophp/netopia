<?php

declare(strict_types=1);

/**
 * Contains the MalformedNetopiaResponse class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-24
 *
 */

namespace Vanilo\Netopia\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class MalformedNetopiaResponse extends HttpResponseException
{
    public static function create(): self
    {
        return new self(
            new JsonResponse(['message' => 'Netopia callback contains an invalid request'], Response::HTTP_BAD_REQUEST)
        );
    }
}
