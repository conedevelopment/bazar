<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\DiscountRepository;
use Illuminate\Support\Facades\Facade;

class Discount extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return DiscountRepository::class;
    }
}
