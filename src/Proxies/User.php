<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\User as Contract;

class User extends Proxy
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Contract::class;
    }
}
