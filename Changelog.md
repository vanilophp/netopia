# Vanilo Netopia Module Changelog

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
