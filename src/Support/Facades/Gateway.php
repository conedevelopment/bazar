<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Gateway\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static array enabled()
 * @method static array disabled()
 * @method static bool has(string $driver)
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
