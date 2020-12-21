<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Transaction as Contract;

class Transaction extends Proxy
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
