<?php

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Contracts\Repositories\MenuRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $route, string $label, array $options)
 * @method static void resource(string $route, string $label, array $options)
 * @method static void remove(string $route)
 * @method static array items()
 *
 * @see \Cone\Bazar\Contracts\Repositories\MenuRepository
 */
class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return MenuRepository::class;
    }
}
