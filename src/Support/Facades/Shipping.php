<?php

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Interfaces\Shipping\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAvailableDrivers(\Cone\Bazar\Interfaces\Itemable $model)
 *
 * @see \Cone\Bazar\Interfaces\Shipping\Manager
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
