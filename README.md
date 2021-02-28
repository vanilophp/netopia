# Netopia Payment Gateway Support for Vanilo

[![Tests](https://img.shields.io/github/workflow/status/vanilophp/netopia/tests/master?style=flat-square)](https://github.com/vanilophp/netopia/actions?query=workflow%3Atests)
[![Packagist Stable Version](https://img.shields.io/packagist/v/vanilo/netopia.svg?style=flat-square&label=stable)](https://packagist.org/packages/vanilo/netopia)
[![StyleCI](https://styleci.io/repos/329267213/shield?branch=master)](https://styleci.io/repos/329267213)
[![Packagist downloads](https://img.shields.io/packagist/dt/vanilo/netopia.svg?style=flat-square)](https://packagist.org/packages/vanilo/netopia)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

This library implements the [Netopia Payment Processor](https://netopia-payments.com) for
[Vanilo Payments](https://vanilo.io/docs/master/payments).

Being a [Concord Module](https://konekt.dev/concord/1.9/modules) it is intended to be used by
Laravel Applications.

## Documentation

Refer to the markdown files in the [docs](docs/) folder.

## To-do

The library works but there's space for improvement.

### 1. Netopia Error Codes

The following list contains the mapping of error codes from Netopia. Right now, the library doesn't
parse these errors, it only detects if it's 0 (success) or not (failed)

```
Error Code Values
0 – approved
16 – card has a risk (i.e. stolen card)
17 – card number is incorrect
18 – closed card
19 – card is expired
20 – insufficient funds
21 – cVV2 code incorrect
22 – issuer is unavailable
32 – amount is incorrect
33 – currency is incorrect
34 – transaction not permitted to cardholder
35 – transaction declined
36 – transaction rejected by antifraud filters
37 – transaction declined (breaking the law)
38 – transaction declined
48 – invalid request
49 – duplicate PREAUTH
50 – duplicate AUTH
51 – you can only CANCEL a preauth order
52 – you can only CONFIRM a preauth order
53 – you can only CREDIT a confirmed order
54 – credit amount is higher than auth amount
55 – capture amount is higher than preauth amount
56 – duplicate request
99 – generic error
```

### 2. Recurring Payments

It's completely untested at the moment, thus it may or may not work

### 3. Usage of Loyalty Points

It's completely untested at the moment.

### 4. Add More Specific Error Test Cases

- Use the test cards from the doc to generate scenarios for various failed cases
- Catch the XML responses, add them to unit tests and check them specifically

**Cases**:

- [X] Successful Payment. Covered.
- [X] Expired Card. Covered.
- [ ] Insufficient founds. No unit test coverage.
- [ ] Incorrect CVV2/CCV. No unit test coverage.
- [ ] Transaction not permitted (eg. card not enrolled). No unit test coverage.
- [ ] Risky card detected (eg. stolen card). No unit test coverage.
- [ ] Error at the origin Bank (eg. can't connect to the Bank). No unit test coverage.

### 5. Test With Real Netopia Keys

During tests, I created a test account at Netopia and generated test keys, to establish
close to real life scenarios during unit tests.

There were issues with simulating the encrypted messages generated by Netopia using the actual keys.
The tests use a locally generated RSA key pair for this functionality. It seems to do the job, but
if we find to cause of the issue, let's use actual Netopia keys.

### 6. Record More Response Details

Netopia returns the following data which we should record locally:

- `pan_masked` (card number digits eg. 4****2806)
- `error_code` - it's recorded as int, but is private. Use enum (see point 4)

### 7. SMS Payment

Netopia supports SMS payments. Likely won't be implemented here as it's more for micro payments,
but hell, who knows 🤷
