<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\User as Contract;

class User extends Proxy
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
