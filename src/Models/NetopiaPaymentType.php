<?php

declare(strict_types=1);

/**
 * Contains the NetopiaPaymentType class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-27
 *
 */

namespace Vanilo\Netopia\Models;

use Konekt\Enum\Enum;

class NetopiaPaymentType extends Enum
{
    public const __DEFAULT = self::CARD;
    public const SMS    = 0x01;
    public const CARD   = 0x02;
}
