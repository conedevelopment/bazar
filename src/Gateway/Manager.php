<?php

namespace Bazar\Gateway;

use Bazar\Contracts\Gateway\Manager as Contract;
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
        return $this->config->get('bazar.gateway.default');
    }

    /**
     * Get all of the created "drivers".
     *
     * @return array
     */
    public function getDrivers(): array
    {
        if (! isset($this->drivers['cash'])) {
            $this->drivers['cash'] = $this->createDriver('cash');
        }

        if (! isset($this->drivers['transfer'])) {
            $this->drivers['transfer'] = $this->createDriver('transfer');
        }

        foreach (array_keys(array_diff_key($this->customCreators, $this->drivers)) as $key) {
            if (! isset($this->drivers[$key])) {
                $this->drivers[$key] = $this->createDriver($key);
            }
        }

        return parent::getDrivers();
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
}
