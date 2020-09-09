<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Gateway\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array methods()
 * @method static bool has(string $method)
 *
 * @see \Bazar\Contracts\Gateway\Manager
 */
class Gateway extends Facade
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
