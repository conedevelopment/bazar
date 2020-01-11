<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Http\ResponseFactory;
use Illuminate\Support\Facades\Facade;

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
