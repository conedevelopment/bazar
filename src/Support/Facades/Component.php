<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Http\ResponseFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void share(array|string $key, mixed $value)
 * @method static mixed getShared(string|null $key)
 * @method static \Bazar\Http\Response render(string $component, array|\Illuminate\Contracts\Support\Arrayable $prop)
 *
 * @see \Bazar\Contracts\Http\ResponseFactory
 */
class Component extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ResponseFactory::class;
    }
}
