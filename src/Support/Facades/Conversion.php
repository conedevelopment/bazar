<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Conversion\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerConversion(string $name, \Closure $callback)
 * @method static void removeConversion(string $name)
 * @method static array getConversions()
 * @method static \Bazar\Contracts\Models\Medium perform(\Bazar\Contracts\Models\Medium $medium)
 *
 * @see \Bazar\Contracts\Conversion\Manager
 */
class Conversion extends Facade
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
