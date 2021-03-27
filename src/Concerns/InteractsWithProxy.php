<?php

namespace Bazar\Concerns;

use Illuminate\Container\Container;

trait InteractsWithProxy
{
    /**
     * The resolve proxy instance.
     *
     * @var object|null
     */
    protected static $proxy = null;

    /**
     * Get the proxied contract.
     *
     * @return string
     */
    abstract public static function getProxiedContract(): string;

    /**
     * Resolve and get the proxy instance.
     *
     * @return object
     */
    public static function proxy(): object
    {
        if (is_null(static::$proxy)) {
            static::$proxy = Container::getInstance()->make(
                static::getProxiedContract()
            );
        }

        return static::$proxy;
    }

    /**
     * Get the proxied class.
     *
     * @return string
     */
    public static function getProxiedClass(): string
    {
        return get_class(static::proxy());
    }
}
