<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Category as Contract;

class Category extends Proxy
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
