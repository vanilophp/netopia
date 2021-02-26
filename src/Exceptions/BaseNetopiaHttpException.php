<?php

declare(strict_types=1);

/**
 * Contains the BaseNetopiaHttpException class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-25
 *
 */

namespace Vanilo\Netopia\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Vanilo\Netopia\Http\Responses\ErrorResponseToNetopia;

abstract class BaseNetopiaHttpException extends HttpResponseException
{
    public function __construct(int $httpStatusCode, string $errorMessage)
    {
        parent::__construct(
            new ErrorResponseToNetopia($httpStatusCode, $errorMessage)
        );
    }
}
