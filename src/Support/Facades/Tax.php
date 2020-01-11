<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\TaxRepository;
use Illuminate\Support\Facades\Facade;

class Tax extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return TaxRepository::class;
    }
}
