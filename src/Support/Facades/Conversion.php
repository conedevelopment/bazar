<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\ConversionRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, \Closure $callback)
 * @method static void remove(string $name)
 * @method static \Bazar\Contracts\Models\Medium perform(\Bazar\Contracts\Models\Medium $medium)
 *
 * @see \Bazar\Contracts\Repositories\ConversionRepository
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
        return ConversionRepository::class;
    }
}
