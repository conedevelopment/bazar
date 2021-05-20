<?php

namespace Bazar\Shipping;

use Bazar\Contracts\Itemable;
use Bazar\Contracts\Shipping\Manager as Contract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->drivers['local-pickup'] = $this->createDriver('local-pickup');
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.shipping.default');
    }

    /**
     * Get the available drivers for the given model.
     *
     * @param  \Bazar\Contracts\Itemable|null  $model
     * @return void
     */
    public function getAvailableDrivers(?Itemable $model = null): array
    {
        foreach (array_keys(array_diff_key($this->customCreators, parent::getDrivers())) as $key) {
            if (! isset($this->drivers[$key])) {
                $this->drivers[$key] = $this->createDriver($key);
            }
        }

        return array_filter(parent::getDrivers(), static function (Driver $driver) use ($model): bool {
            return is_null($model) ? $driver->enabled() : $driver->available($model);
        });
    }

    /**
     * Create the local pickup driver.
     *
     * @return \Bazar\Shipping\LocalPickupDriver
     */
    public function createLocalPickupDriver(): LocalPickupDriver
    {
        return new LocalPickupDriver(
            $this->config->get('bazar.shipping.drivers.local-pickup', [])
        );
    }
}
