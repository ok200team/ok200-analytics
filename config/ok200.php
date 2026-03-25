<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OK200 API Token
    |--------------------------------------------------------------------------
    |
    | Your API token for the OK200 analytics platform.
    |
    */
    'token' => env('OK200_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | OK200 API Endpoint
    |--------------------------------------------------------------------------
    |
    | The endpoint for the OK200 analytics API.
    |
    */
    'endpoint' => env('OK200_API_ENDPOINT', 'https://platform.ok200.net/api/v1/analytics'),

    /*
    |--------------------------------------------------------------------------
    | Domain Identifier
    |--------------------------------------------------------------------------
    |
    | The domain identifier sent with each analytics event.
    | Defaults to APP_URL if not set.
    |
    */
    'domain' => env('OK200_DOMAIN', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | Production Only
    |--------------------------------------------------------------------------
    |
    | When true, analytics events are only sent in the production environment.
    | Set to false for testing/debugging.
    |
    */
    'production_only' => env('OK200_PRODUCTION_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Event Mappings
    |--------------------------------------------------------------------------
    |
    | Map your application's events to OK200 analytics tracking.
    | Each key is the analytics event type, and the value is your
    | application's event class. The event must have a public
    | method or property that returns the user's email.
    |
    | Supported event types: 'login', 'user', 'order'
    |
    | Example:
    |   'login' => [
    |       'event' => \App\Events\UserLoggedIn::class,
    |       'email' => 'user.email',  // dot notation to resolve email
    |   ],
    |
    */
    'events' => [
        // 'login' => [
        //     'event' => \App\Events\UserLoggedIn::class,
        //     'email' => 'user.email',
        // ],
        // 'user' => [
        //     'event' => \App\Events\UserWasCreated::class,
        //     'email' => 'user.email',
        // ],
        // 'order' => [
        //     'event' => \App\Events\OrderPlaced::class,
        //     'email' => 'user.email',
        //     'order_value' => 'order.amount',
        //     'order_id' => 'order.id',
        // ],
    ],

];
