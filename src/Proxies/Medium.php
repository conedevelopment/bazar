<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Medium as Contract;

class Medium extends Proxy
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
