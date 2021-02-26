<?php

declare(strict_types=1);

/**
 * Contains the NetopiaDecryptionException class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-26
 *
 */

namespace Vanilo\Netopia\Exceptions;

class NetopiaDecryptionException extends BaseNetopiaHttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Failed to decrypt the message');
    }
}
