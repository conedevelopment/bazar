<?php

namespace Bazar\Gateway;

use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Bazar\Support\Facades\Gateway;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Indicates if the driver is enabled.
     *
     * @var bool
     */
    protected bool $enabled = true;

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get the ID of the driver.
     *
     * @return string
     */
    public function id(): string
    {
        return array_search($this, Gateway::all());
    }

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    public function name(): string
    {
        return preg_replace(
            '/([a-z0-9])([A-Z])/', '$1 $2', str_replace('Driver', '', class_basename($this))
        );
    }

    /**
     * Determine if the driver is enabled.
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Determine if the driver is disabled.
     *
     * @return bool
     */
    public function disabled(): bool
    {
        return ! $this->enabled;
    }

    /**
     * Enable the gateway.
     *
     * @return $this
     */
    public function enable(): Driver
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Disable the gateway.
     *
     * @return $this
     */
    public function disable(): Driver
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * Get the URL of the transaction.
     *
     * @param  \Bazar\Models\Transaction  $transaction
     * @return string|null
     */
    public function transactionUrl(Transaction $transaction): ?string
    {
        return null;
    }

    /**
     * Process the payment.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Models\Transaction
     */
    abstract public function pay(Order $order, ?float $amount = null): Transaction;

    /**
     * Process the refund.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Models\Transaction
     */
    abstract public function refund(Order $order, ?float $amount = null): Transaction;
}
