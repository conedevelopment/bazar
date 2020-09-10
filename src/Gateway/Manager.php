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
        return array_filter($this->all(), function (Driver $driver) {
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
        return array_filter($this->all(), function (Driver $driver) {
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
