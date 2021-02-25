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

use Symfony\Component\HttpFoundation\Response;

final class MalformedNetopiaResponse extends BaseNetopiaHttpException
{
    public static function create(): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            'Netopia callback contains an invalid request',
        );
    }
}
