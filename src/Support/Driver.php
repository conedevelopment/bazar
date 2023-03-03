<?php

namespace Cone\Bazar\Support;

use Cone\Bazar\Interfaces\Itemable;
use Illuminate\Support\Str;

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
        $name = Str::of(static::class)->classBasename()->replace('Driver', '')->headline()->value();

        return __($name);
    }
}
