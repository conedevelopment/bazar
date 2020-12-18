<?php

namespace Bazar\Proxies;

use Illuminate\Support\Facades\Facade;

abstract class Proxy extends Facade
{
    /**
     * Get the proxied contract.
     *
     * @return string
     */
    public static function getProxiedContract(): string
    {
        return static::getFacadeAccessor();
    }

    /**
     * Get the proxied instance.
     *
     * @return object
     */
    public static function getProxiedInstance(): object
    {
        return static::getFacadeRoot();
    }

    /**
     * Get the proxied class.
     *
     * @return string
     */
    public static function getProxiedClass(): string
    {
        return get_class(static::getProxiedInstance());
    }
}
