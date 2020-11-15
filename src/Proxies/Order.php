<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Order as Contract;

class Order extends Proxy
{
    /**
     * Get the proxied contract.
     *
     * @return string
     */
    public static function getProxiedContract(): string
    {
        return Contract::class;
    }
}
