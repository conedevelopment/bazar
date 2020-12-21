<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Order as Contract;

class Order extends Proxy
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
