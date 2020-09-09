<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Shipping\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array methods()
 * @method static bool has(string $method)
 *
 * @see \Bazar\Contracts\Shipping\Manager
 */
class Shipping extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
