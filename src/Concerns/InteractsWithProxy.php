<?php

namespace Cone\Bazar\Concerns;

use Illuminate\Container\Container;

trait InteractsWithProxy
{
    /**
     * The resolve proxy instance.
     *
     * @var object
     */
    protected static object $proxy;

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
        if (! isset(static::$proxy)) {
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
