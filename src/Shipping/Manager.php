<?php

namespace Bazar\Shipping;

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
        $this->drivers['weight-based-shipping'] = $this->createDriver('weight-based-shipping');
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
     * Get all drivers.
     *
     * @return array
     */
    public function all(): array
    {
        foreach (array_keys(array_diff_key($this->customCreators, $this->drivers)) as $key) {
            $this->drivers[$key] = $this->callCustomCreator($key);
        }

        return $this->getDrivers();
    }

    /**
     * Get the enabled drivers.
     *
     * @return array
     */
    public function enabled(): array
    {
        return array_filter($this->all(), static function (Driver $driver): bool {
            return $driver->enabled();
        });
    }

    /**
     * Get the disabled drivers.
     *
     * @return array
     */
    public function disabled(): array
    {
        return array_filter($this->all(), static function (Driver $driver): bool {
            return $driver->disabled();
        });
    }

    /**
     * Determine if the given driver exists.
     *
     * @param  string  $driver
     * @return bool
     */
    public function has(string $driver): bool
    {
        return isset($this->drivers[$driver]) || isset($this->customCreators[$driver]);
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

    /**
     * Create the weight based shipping driver.
     *
     * @return \Bazar\Shipping\WeightBasedShippingDriver
     */
    public function createWeightBasedShippingDriver(): WeightBasedShippingDriver
    {
        return new WeightBasedShippingDriver(
            $this->config->get('bazar.shipping.drivers.weight-based-shipping', [])
        );
    }
}
