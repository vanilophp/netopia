<?php

declare(strict_types=1);

/**
 * Contains the InvalidNetopiaKeyException class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-24
 *
 */

namespace Vanilo\Netopia\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class InvalidNetopiaKeyException extends BaseNetopiaHttpException
{
    public static function fromPath(string $path): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            "The public key file at `$path` is invalid",
        );
    }
}
