<?php

namespace Cone\Bazar\Support;

use Cone\Bazar\Interfaces\Itemable;

abstract class Driver
{
    /**
     * Indicates if the driver is enabled.
     *
     * @var bool
     */
    protected bool $enabled = true;

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
     * Determine if the driver is available for the given model.
     *
     * @param  \Cone\Bazar\Interfaces\Itemable  $model
     * @return bool
     */
    public function available(Itemable $model): bool
    {
        return $this->enabled();
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
        return ! $this->enabled();
    }

    /**
     * Enable the gateway.
     *
     * @return void
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable the gateway.
     *
     * @return void
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    public function getName(): string
    {
        $name = preg_replace(
            '/([a-z0-9])([A-Z])/', '$1 $2', str_replace('Driver', '', class_basename(static::class))
        );

        return __($name);
    }
}
