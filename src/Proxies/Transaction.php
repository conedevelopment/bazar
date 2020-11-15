<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Transaction as Contract;

class Transaction extends Proxy
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
