<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Interfaces\Gateway\Manager as Contract;
use Cone\Bazar\Interfaces\Itemable;
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

        $this->drivers['cash'] = $this->createDriver('cash');
        $this->drivers['transfer'] = $this->createDriver('transfer');
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.gateway.default');
    }

    /**
     * Get the available drivers for the given model.
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
     * Create the transfer driver.
     */
    public function createTransferDriver(): TransferDriver
    {
        return new TransferDriver(
            $this->config->get('bazar.gateway.drivers.transfer', [])
        );
    }

    /**
     * Create the cash driver.
     */
    public function createCashDriver(): CashDriver
    {
        return new CashDriver(
            $this->config->get('bazar.gateway.drivers.cash', [])
        );
    }
}
