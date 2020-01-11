<?php

namespace Bazar\Shipping;

use Bazar\Contracts\Shippable;
use Bazar\Support\Facades\Shipping;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Indicates if the driver is enabled.
     *
     * @var bool
     */
    protected $enabled = true;

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
     * Get the ID of the driver.
     *
     * @return string
     */
    public function id(): string
    {
        return array_search($this, Shipping::methods());
    }

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    public function name(): string
    {
        return preg_replace(
            '/([a-z0-9])([A-Z])/', '$1 $2', str_replace('Driver', '', class_basename($this))
        );
    }

    /**
     * Determine if the driver is enabled.
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Determine if the driver is disabled.
     *
     * @return bool
     */
    public function disabled(): bool
    {
        return ! $this->enabled;
    }

    /**
     * Enable the gateway.
     *
     * @return $this
     */
    public function enable(): Driver
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Disable the gateway.
     *
     * @return $this
     */
    public function disable(): Driver
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * Calculate the shipping cost.
     *
     * @param  \Bazar\Contracts\Shippable  $model
     * @return float
     */
    abstract public function calculate(Shippable $model): float;
}
