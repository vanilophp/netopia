<?php

declare(strict_types=1);

/**
 * Contains the BaseResponseToNetopia class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-26
 *
 */

namespace Vanilo\Netopia\Http\Responses;

use Illuminate\Http\Response;

abstract class BaseResponseToNetopia extends Response
{
    public function __construct(int $httpStatusCode, string $message, array $crcAttributes = [])
    {
        $attrs = '';
        foreach ($crcAttributes as $key => $value) {
            $attrs .= " $key=\"$value\"";
        }

        parent::__construct(
            "<?xml version=\"1.0\" encoding=\"utf-8\" ?><crc$attrs>$message</crc>",
            $httpStatusCode,
            ['Content-type' => 'application/xml; charset="utf-8"']
        );
    }
}
