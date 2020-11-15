<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Address as Contract;

class Address extends Proxy
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
