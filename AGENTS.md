# sdk-sportypay-php

PHP client for the SportyPay API. Composer package `sportyneo/php-sdk`. PHP `^8.2`. No development dependencies, test suite, or CI config are committed.

## Source

PSR-4 `Sportyneo\SDK\` maps to `src/`.

- `src/Client/Client.php` — client class `Sportyneo\SDK\Client\Client`
- `src/Resources/` — entities, shops, customers, orders, users, statistics, shop stats, payments, invitations
- `src/Contracts/` — enums shared with the API (payment methods, instalments, order status, PSP, discounts)
- `src/Exceptions/` — `ApiException`, `AuthenticationException`, `ValidationException`, `NotFoundException`

Endpoint notes live in `docs/` (`orders.md`, `shops.md`, `payments.md`, `sales-attests.md`, and the other resource files). `CHANGELOG.md` is empty.

The README still describes a `SportyneoClient` class and PHP `>= 7.4`. The class and PHP constraint above are the ones in `composer.json` and `src/`. Order amounts in `docs/orders.md` are integer cents.
