<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Product as Contract;

class Product extends Proxy
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
