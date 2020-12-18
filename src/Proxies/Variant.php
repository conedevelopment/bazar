<?php

namespace Bazar\Proxies;

use Bazar\Contracts\Models\Variant as Contract;

class Variant extends Proxy
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
