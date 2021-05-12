<?php

namespace Bazar\Gateway;

use Bazar\Events\CheckoutFailed;
use Bazar\Events\CheckoutProcessing;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Bazar\Support\Facades\Gateway;
use Illuminate\Http\Request;
use Throwable;

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
    public function enable(): self
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Disable the gateway.
     *
     * @return $this
     */
    public function disable(): self
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
     * Handle the checkout request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Cart  $cart
     * @return \Bazar\Models\Order
     */
    public function checkout(Request $request, Cart $cart): Order
    {
        $order = $cart->toOrder();

        try {
            CheckoutProcessing::dispatch($order);

            $this->pay($order);
        } catch (Throwable $exception) {
            CheckoutFailed::dispatch($order);
        }

        return $order;
    }
}
