<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Meta as Contract;

class Meta extends Proxy
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
