<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Variation as Contract;

class Variation extends Proxy
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
