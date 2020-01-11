<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Gateway\Manager;
use Illuminate\Support\Facades\Facade;

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
