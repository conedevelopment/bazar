<?php

namespace Cone\Bazar\Shipping;

use Cone\Bazar\Interfaces\Itemable;
use Cone\Bazar\Interfaces\Shipping\Manager as Contract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
    /**
     * Create a new manager instance.
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->drivers['local-pickup'] = $this->createDriver('local-pickup');
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.shipping.default');
    }

    /**
     * Get the available drivers for the given model.
     */
    public function getAvailableDrivers(Itemable $model = null): array
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
     */
    public function createLocalPickupDriver(): LocalPickupDriver
    {
        return new LocalPickupDriver(
            $this->config->get('bazar.shipping.drivers.local-pickup', [])
        );
    }
}
