<?php

namespace Cone\Bazar\Support;

use Cone\Bazar\Interfaces\Itemable;

abstract class Driver
{
    /**
     * Indicates if the driver is enabled.
     */
    protected bool $enabled = true;

    /**
     * The driver config.
     */
    protected array $config = [];

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Determine if the driver is available for the given model.
     */
    public function available(Itemable $model): bool
    {
        return $this->enabled();
    }

    /**
     * Determine if the driver is enabled.
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Determine if the driver is disabled.
     */
    public function disabled(): bool
    {
        return ! $this->enabled();
    }

    /**
     * Enable the gateway.
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable the gateway.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Get the name of the driver.
     */
    public function getName(): string
    {
        $name = preg_replace(
            '/([a-z0-9])([A-Z])/', '$1 $2', str_replace('Driver', '', class_basename(static::class))
        );

        return __($name);
    }
}
