# OK200 Analytics

A Laravel package for sending analytics events to the OK200 platform.

## Installation

This package is not available on Packagist. To install it, add the GitHub repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ok200/ok200-analytics.git"
        }
    ]
}
```

Then require the package:

```bash
composer require ok200/analytics:dev-main
```

> If the repository is private, make sure Composer has access via an SSH key or a [GitHub OAuth token](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth).

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=ok200-config
```

Add your API token to `.env`:

```env
OK200_API_TOKEN=your-token-here
```

### Optional environment variables

```env
OK200_API_ENDPOINT=https://platform.ok200.net/api/v1/analytics
OK200_DOMAIN=your-app-domain
OK200_PRODUCTION_ONLY=true
```

## Usage

There are two ways to use this package:

### Option 1: Dispatch jobs directly

The simplest approach — dispatch the jobs from wherever makes sense in your app:

```php
use Ok200\Analytics\Jobs\RecordLogin;
use Ok200\Analytics\Jobs\RecordOrder;
use Ok200\Analytics\Jobs\RecordUser;

// Record a login
RecordLogin::dispatch(sha1($user->email));

// Record a new user
RecordUser::dispatch(sha1($user->email));

// Record an order
RecordOrder::dispatch(
    userSha1: sha1($user->email),
    orderValue: 4999,
    orderId: 'ord_abc123',
);
```

### Option 2: Automatic event mapping

Map your application's events in `config/ok200.php` and the package will listen for them automatically:

```php
'events' => [
    'login' => [
        'event' => \App\Events\UserLoggedIn::class,
        'email' => 'user.email',
    ],
    'user' => [
        'event' => \App\Events\UserWasCreated::class,
        'email' => 'user.email',
    ],
    'order' => [
        'event' => \App\Events\OrderPlaced::class,
        'email' => 'user.email',
        'order_value' => 'order.amount',
        'order_id' => 'order.id',
    ],
],
```

The `email`, `order_value`, and `order_id` values use dot notation to resolve properties from the event object. For example, `'user.email'` resolves `$event->user->email`.

## Rate Limiting

All jobs handle 429 responses from the OK200 API by automatically retrying after 2 minutes.
