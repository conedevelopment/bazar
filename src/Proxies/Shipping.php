<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Shipping as Contract;

class Shipping extends Proxy
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
