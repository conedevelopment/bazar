<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Product as Contract;

class Product extends Proxy
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
