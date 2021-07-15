<?php

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Contracts\Shipping\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAvailableDrivers(\Cone\Bazar\Contracts\Itemable $model)
 *
 * @see \Cone\Bazar\Contracts\Shipping\Manager
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
