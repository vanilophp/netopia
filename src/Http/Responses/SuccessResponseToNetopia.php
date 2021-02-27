<?php

declare(strict_types=1);

/**
 * Contains the SuccessResponseToNetopia class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-26
 *
 */

namespace Vanilo\Netopia\Http\Responses;

class SuccessResponseToNetopia extends BaseResponseToNetopia
{
    public function __construct(string $message = null)
    {
        if (null === $message) {
            $message = "Confirmation successfully received";
        }

        parent::__construct(200, $message);
    }
}
