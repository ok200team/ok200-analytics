<?php

namespace Ok200\Analytics\Facades;

use Illuminate\Support\Facades\Facade;
use Ok200\Analytics\Ok200Client;

/**
 * @method static bool send(array $payload)
 *
 * @see \Ok200\Analytics\Ok200Client
 */
class Ok200Analytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Ok200Client::class;
    }
}
