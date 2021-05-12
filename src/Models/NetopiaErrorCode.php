<?php

declare(strict_types=1);

/**
 * Contains the NetopiaErrorCode class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-05-12
 *
 */

namespace Vanilo\Netopia\Models;

use Konekt\Enum\Enum;

final class NetopiaErrorCode extends Enum
{
    public const __DEFAULT = self::UNKNOWN;

    public const UNKNOWN = null;

    public const APPROVED = 0;
    public const CARD_RISK = 16;
    public const INCORRECT_CARD_NUMBER = 17;
    public const CARD_CLOSED = 18;
    public const CARD_EXPIRED = 19;
    public const INSUFFICIENT_FUNDS = 20;
    public const INCORRECT_CVV2 = 21;
    public const ISSUER_UNAVAILABLE = 22;
    public const INCORRECT_AMOUNT = 32;
    public const INCORRECT_CURRENCY = 33;
    public const TRANSACTION_NOT_PERMITTED = 34;
    public const TRANSACTION_DECLINED_35 = 35;
    public const TRANSACTION_REJECTED_ANTIFRAUD = 36;
    public const TRANSACTION_DECLINED_LAW = 37;
    public const TRANSACTION_DECLINED_38 = 38;
    public const INVALID_REQUEST = 48;
    public const DUPLICATE_PREAUTH = 49;
    public const DUPLICATE_AUTH = 50;
    public const PREAUTH_CAN_ONLY_BE_CANCELLED = 51;
    public const PREAUTH_CAN_ONLY_BE_CONFIRMED = 52;
    public const PREAUTH_CAN_ONLY_BE_CREDITED = 53;
    public const CREDIT_AMOUNT_IS_HIGHER_THAN_AUTH_AMOUNT = 54;
    public const CAPTURE_AMOUNT_IS_HIGHER_THAN_PREAUTH_AMOUNT = 55;
    public const DUPLICATE_REQUEST = 56;
    public const GENERIC_ERROR = 99;

    protected static $unknownValuesFallbackToDefault = true;

    protected static array $labels = [];

    protected static function boot()
    {
        static::$labels = [
            self::UNKNOWN => __('Unknown Netopia Error'),
            self::APPROVED => __('Approved'),
            self::CARD_RISK => __('Card has a risk (i.e. stolen card)'),
            self::INCORRECT_CARD_NUMBER => __('Card number is incorrect'),
            self::CARD_CLOSED => __('Closed card'),
            self::CARD_EXPIRED => __('Card is expired'),
            self::INSUFFICIENT_FUNDS => __('Insufficient funds'),
            self::INCORRECT_CVV2 => __('CVV2 code incorrect'),
            self::ISSUER_UNAVAILABLE => __('Issuer is unavailable'),
            self::INCORRECT_AMOUNT => __('Amount is incorrect'),
            self::INCORRECT_CURRENCY => __('Currency is incorrect'),
            self::TRANSACTION_NOT_PERMITTED => __('Transaction not permitted to cardholder'),
            self::TRANSACTION_DECLINED_35 => __('Transaction declined'),
            self::TRANSACTION_REJECTED_ANTIFRAUD => __('Transaction rejected by antifraud filters'),
            self::TRANSACTION_DECLINED_LAW => __('Transaction declined (breaking the law)'),
            self::TRANSACTION_DECLINED_38 => __('Transaction declined'),
            self::INVALID_REQUEST => __('Invalid request'),
            self::DUPLICATE_PREAUTH => __('Duplicate PREAUTH'),
            self::DUPLICATE_AUTH => __('Duplicate AUTH'),
            self::PREAUTH_CAN_ONLY_BE_CANCELLED => __('You can only CANCEL a preauth order'),
            self::PREAUTH_CAN_ONLY_BE_CONFIRMED => __('You can only CONFIRM a preauth order'),
            self::PREAUTH_CAN_ONLY_BE_CREDITED => __('You can only CREDIT a confirmed order'),
            self::CREDIT_AMOUNT_IS_HIGHER_THAN_AUTH_AMOUNT => __('Credit amount is higher than auth amount'),
            self::CAPTURE_AMOUNT_IS_HIGHER_THAN_PREAUTH_AMOUNT => __('Capture amount is higher than preauth amount'),
            self::DUPLICATE_REQUEST => __('Duplicate request'),
            self::GENERIC_ERROR => __('Generic error'),
        ];
    }
}
