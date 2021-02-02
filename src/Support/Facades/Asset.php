<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\AssetRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, string $source, string $type, array $options)
 * @method static void script(string $name, string $source, array $options)
 * @method static void style(string $name, string $source, array $options)
 * @method static void icon(string $name, string $source, array $options)
 * @method static void remove(string $name)
 * @method static array scripts()
 * @method static array styles()
 * @method static array icons()
 * @method static void link()
 *
 * @see \Bazar\Contracts\Repositories\AssetRepository
 */
class Asset extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return AssetRepository::class;
    }
}
