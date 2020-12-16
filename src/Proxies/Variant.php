<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Variant as Contract;

class Variant extends Proxy
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
