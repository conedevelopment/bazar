<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Shipping as Contract;

class Shipping extends Proxy
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
