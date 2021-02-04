<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Conversion\Manager;
use Closure;
use Illuminate\Support\Facades\Facade;

/**
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

    /**
     * Register a new conversion.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public static function register(string $name, Closure $callback): void
    {
        static::getFacadeRoot()->registerConversion($name, $callback);
    }

    /**
     * Remove the given conversion.
     *
     * @param  string  $name
     * @return void
     */
    public static function remove(string $name): void
    {
        static::getFacadeRoot()->removeConversion($name);
    }

    /**
     * Get all the registered conversions.
     *
     * @return array
     */
    public static function all(): array
    {
        return static::getFacadeRoot()->getConversions();
    }
}
