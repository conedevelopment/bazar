<?php

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Interfaces\Gateway\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAvailableDrivers(\Cone\Bazar\Interfaces\Checkoutable $model)
 * @method static \Cone\Bazar\Gateway\Driver driver(string $driver)
 *
 * @see \Cone\Bazar\Interfaces\Gateway\Manager
 */
class Gateway extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
