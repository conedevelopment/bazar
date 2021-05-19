<?php

namespace Bazar\Shipping;

use Bazar\Contracts\Shipping\Manager as Contract;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
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
     * Get all of the created "drivers".
     *
     * @return array
     */
    public function getDrivers(): array
    {
        if (! isset($this->drivers['local-pickup'])) {
            $this->drivers['local-pickup'] = $this->createDriver('local-pickup');
        }

        foreach (array_keys(array_diff_key($this->customCreators, $this->drivers)) as $key) {
            if (! isset($this->drivers[$key])) {
                $this->drivers[$key] = $this->createDriver($key);
            }
        }

        return parent::getDrivers();
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
