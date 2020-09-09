<?php

namespace Bazar\Gateway;

use Bazar\Contracts\Gateway\Manager as Contract;
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

        $this->drivers['cash'] = $this->createDriver('cash');
        $this->drivers['transfer'] = $this->createDriver('transfer');
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.gateway.default');
    }

    /**
     * Get all the enabled gateway drivers.
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
     * Create the transfer driver.
     *
     * @return \Bazar\Gateway\TransferDriver
     */
    public function createTransferDriver(): TransferDriver
    {
        return new TransferDriver(
            $this->config->get('bazar.gateway.drivers.transfer', [])
        );
    }

    /**
     * Create the cash driver.
     *
     * @return \Bazar\Gateway\CashDriver
     */
    public function createCashDriver(): CashDriver
    {
        return new CashDriver(
            $this->config->get('bazar.gateway.drivers.cash', [])
        );
    }

    /**
     * Create the manual driver.
     *
     * @return \Bazar\Gateway\ManualDriver
     */
    public function createManualDriver(): ManualDriver
    {
        return new ManualDriver;
    }
}
