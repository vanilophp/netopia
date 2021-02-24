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

use RuntimeException;

final class InvalidNetopiaKeyException extends RuntimeException
{
    public static function fromPath(string $path): self
    {
        return new self("The public key file at `$path` is invalid");
    }
}
