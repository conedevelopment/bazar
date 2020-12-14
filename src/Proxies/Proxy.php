<?php

namespace Bazar\Proxies;

use Illuminate\Container\Container;

abstract class Proxy
{
    /**
     * The resolved instances.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Get the proxied contract.
     *
     * @return string
     */
    abstract public static function getProxiedContract(): string;

    /**
     * Get the proxied instance.
     *
     * @return object
     */
    public static function getProxiedInstance(): object
    {
        $contract = static::getProxiedContract();

        if (! isset(static::$instances[$contract])) {
            static::$instances[$contract] = Container::getInstance()->make($contract);
        }

        return static::$instances[$contract];
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

    /**
     * Handle dynamic method calls into the proxied instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        return call_user_func_array([static::getProxiedClass(), $method], $parameters);
    }
}
