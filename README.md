# sdk-sportypay-php (ex: sportyneo-client-sdk)

PHP client for the SportyPay API. It covers entities, clubs, customers, orders, users, payments, invitations, and statistics.

The HTTP reference lives in [`docs/`](docs/basic-usage.md). Amounts are integers in euro cents (`15000` = €150.00).

## Environments

| Environment | URL |
| --- | --- |
| Production | `<PRODUCTION_URL>` |
| Staging | `<STAGING_URL>` |

Pass the matching base URL to the client. The default in code is `https://api.sportyneo.com`.

## Requirements

- PHP 8.2 or later
- cURL
- JSON

## Installation

```bash
composer require sportyneo/php-sdk
```

From this monorepo, depend on the package with a path repository instead of Packagist.

## Usage

```php
use Sportyneo\SDK\Client\Client;

$client = new Client(
    email: 'your-email@example.com',
    password: 'your-password',
    entityId: 123,
    baseUrl: '<PRODUCTION_URL>',
);

$client->setTimeout(120);
$client->setDebug(true);

$shops = $client->shops->all(['page' => 1, 'per_page' => 20]);

$customer = $client->customers->create([
    'mail' => 'member@example.com',
    'first_name' => 'Marie',
    'surname' => 'Martin',
]);
```

Resources on the client: `entities`, `shops`, `customers`, `orders`, `users`, `statistics`, `shopStats`, `payments`, `invitations`.

## Errors

| Exception | HTTP status |
| --- | --- |
| `Sportyneo\SDK\Exceptions\AuthenticationException` | 401 |
| `Sportyneo\SDK\Exceptions\NotFoundException` | 404 |
| `Sportyneo\SDK\Exceptions\ValidationException` | 422 |
| `Sportyneo\SDK\Exceptions\ApiException` | other |

`ValidationException::getErrors()` returns field messages.

## Documentation

| Guide | Topic |
| --- | --- |
| [docs/basic-usage.md](docs/basic-usage.md) | Auth, headers, pagination, errors |
| [docs/configuration.md](docs/configuration.md) | Enums and configuration |
| [docs/entities.md](docs/entities.md) | Entities |
| [docs/shops.md](docs/shops.md) | Clubs |
| [docs/customers.md](docs/customers.md) | Customers |
| [docs/orders.md](docs/orders.md) | Orders |
| [docs/payments.md](docs/payments.md) | Payment sessions |
| [docs/psp.md](docs/psp.md) | PSP onboarding |
| [docs/invitations.md](docs/invitations.md) | User invitations |
| [docs/users.md](docs/users.md) | Users |
| [docs/statistics.md](docs/statistics.md) | Statistics |
| [docs/cumulus.md](docs/cumulus.md) | Weekly payouts |
| [docs/sales-attests.md](docs/sales-attests.md) | Sales certificates |
