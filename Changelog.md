# Vanilo Netopia Module Changelog

## Unreleased
##### 2022-06-XX

- Dropped Laravel 8 support
- Dropped PHP 7.4 support
- Dropped Vanilo 2.x support

## 1.2.0
##### 2022-06-15

- Dropped Laravel 6-7 support
- Changed minimum Laravel version to 8.22.1, to enforce the [CVE-2021-21263](https://blog.laravel.com/security-laravel-62011-7302-8221-released) security patch
- Added Laravel 9 support
- Added Vanilo 3.x support

## 1.1.1
##### 2022-06-15

- Locked Enum to v3 only (`NetopiaAction` is incompatible with Enum v4 due to missing `bool` type on `$unknownValues...` attribute)
- Tested with PHP 8.1 (works)

## 1.1.0
##### 2021-09-11

- Requires Vanilo 2.2
- Added mapping of payment response result to PaymentStatus
- Added NativeStatus (returns NetopiaAction) to PaymentResponse
- Added automatic conversion to full App URLs when return/confirm URL are paths
- Improved transaction status messages via handling of Netopia error codes
- Changed to return payment response's amount paid as negative in refund and cancelled transactions (to comply with Payment v2.2 default handler)

## 1.0.0
##### 2021-03-02

- Initial release. Works.
- Requires Vanilo 2.1
