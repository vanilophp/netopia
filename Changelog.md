# Vanilo Netopia Module Changelog

## Unreleased
##### 2024-XX-YY

- Added support for passing `buttonText` and `buttonClass` variables to payment request's getHtmlSnippet() method
- Added transaction handler with retry payment support
- Changed minimum Vanilo requirement to v4.1

## 3.0.0
##### 2024-04-25

- Added Vanilo 4 support
- Added Laravel 11 support
- Dropped Vanilo 3 support
- Dropped PHP 8.0 and 8.1 support
- Dropped Laravel 9 support
- Changed the minimal Laravel version requirement to v10.43

---

## 2.X Series

## 2.1.1
##### 2023-12-17

- Added PHP 8.3 support

## 2.1.0
##### 2023-05-26

- Added PHP 8.2 support
- Bumped minimal Laravel version to v9.2
- Added Laravel 10 Support

## 2.0.0
##### 2022-06-15

- Added Enum v4 support
- Dropped Enum v3 support
- Changed minimum Concord version to v1.11
- Rest is identical with 1.3

---

## 1.X Series

## 1.4.0
##### 2023-05-26

- Added PHP 8.2 support
- Added Laravel 10 support
- Bumped minimal Laravel version to v9.2

## 1.3.0
##### 2022-06-15

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
