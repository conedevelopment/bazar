<?php

namespace Bazar\Gateway;

use Bazar\Contracts\Models\Order;
use Bazar\Contracts\Models\Transaction;
use Bazar\Exceptions\TransactionFailedException;
use Bazar\Support\Facades\Gateway;
use InvalidArgumentException;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Indicates if the driver is enabled.
     *
     * @var bool
     */
    protected $enabled = true;

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
     * @param  \Bazar\Contracts\Models\Transaction  $transaction
     * @return string|null
     */
    public function transactionUrl(Transaction $transaction): ?string
    {
        return null;
    }

    /**
     * Make a transaction for the given order.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @param  string  $type
     * @param  float|null  $amount
     * @return \Bazar\Contracts\Models\Transaction
     *
     * @throws \InvalidArgumentException
     * @throws \Bazar\Exceptions\TransactionFailedException
     */
    public function transaction(Order $order, string $type = 'payment', float $amount = null): Transaction
    {
        if (! in_array($type, ['payment', 'refund'])) {
            throw new InvalidArgumentException('The transaction type must be "payment" or "refund".');
        }

        if ($type === 'payment' && $order->paid()) {
            throw new TransactionFailedException("The order #{$order->id} is fully paid.");
        }

        if ($type === 'refund' && $order->refunded()) {
            throw new TransactionFailedException("The order #{$order->id} is fully refunded.");
        }

        $total = $type === 'payment' ? $order->totalPayable() : $order->totalRefundable();

        $transaction = $order->transactions()->make([
            'type' => $type,
            'driver' => $this->id(),
            'amount' => is_null($amount) ? $total : min($amount, $total),
        ]);

        $order->transactions->push($transaction);

        return $transaction;
    }

    /**
     * Process the payment.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Contracts\Models\Transaction
     */
    abstract public function pay(Order $order, float $amount = null): Transaction;

    /**
     * Process the refund.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Contracts\Models\Transaction
     */
    abstract public function refund(Order $order, float $amount = null): Transaction;
}
