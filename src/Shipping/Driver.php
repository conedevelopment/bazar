<?php

namespace Bazar\Shipping;

use Bazar\Contracts\Shippable;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Calculate the shipping cost.
     *
     * @param  \Bazar\Contracts\Shippable  $model
     * @return float
     */
    abstract public function calculate(Shippable $model): float;

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    public function getName(): string
    {
        return preg_replace(
            '/([a-z0-9])([A-Z])/', '$1 $2', str_replace('Driver', '', class_basename(static::class))
        );
    }
}
