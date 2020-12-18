<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Option as Contract;
use Illuminate\Support\Facades\Facade;

class Option extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Contract::class;
    }
}
