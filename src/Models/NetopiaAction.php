<?php

declare(strict_types=1);

/**
 * Contains the NetopiaAction class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-27
 *
 */

namespace Vanilo\Netopia\Models;

use Konekt\Enum\Enum;

/**
 * @method static NetopiaAction UNKNOWN()
 * @method static NetopiaAction NEW()
 * @method static NetopiaAction PAID_PENDING()
 * @method static NetopiaAction CONFIRMED_PENDING()
 * @method static NetopiaAction PAID()
 * @method static NetopiaAction CONFIRMED()
 * @method static NetopiaAction CREDIT()
 * @method static NetopiaAction CANCELED()
 * 
 * @method bool isUnknown()
 * @method bool isNew()
 * @method bool isPaidPending()
 * @method bool isConfirmedPending()
 * @method bool isPaid()
 * @method bool isConfirmed()
 * @method bool isCredit()
 * @method bool isCanceled()
 */
class NetopiaAction extends Enum
{
    public const __DEFAULT = self::UNKNOWN;
    public const UNKNOWN = 'unknown';
    public const NEW = 'new';
    public const PAID_PENDING = 'paid_pending';
    public const CONFIRMED_PENDING = 'confirmed_pending';
    public const PAID = 'paid';
    public const CONFIRMED = 'confirmed';
    public const CREDIT = 'credit';
    public const CANCELED = 'canceled';

    protected static $unknownValuesFallbackToDefault = true;
}
