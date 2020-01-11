<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\ConversionRepository;
use Illuminate\Support\Facades\Facade;

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
