<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Gateway\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAvailableDrivers(\Bazar\Contracts\Itemable $model)
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
