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
     * Get all the shipping methods.
     *
     * @return array
     */
    public function methods(): array
    {
        $drivers = array_reduce(array_keys($this->customCreators), function ($drivers, $driver) {
            return array_merge($drivers, [$driver => $this->callCustomCreator($driver)]);
        }, []);

        return array_filter(array_replace($this->getDrivers(), $drivers), function ($driver) {
            return $driver->enabled();
        });
    }

    /**
     * Determine if the given method exists.
     *
     * @param  string  $method
     * @return bool
     */
    public function has(string $method): bool
    {
        return isset($this->drivers[$method]) || isset($this->customCreators[$method]);
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
