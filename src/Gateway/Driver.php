<?php

namespace Bazar\Gateway;

use Bazar\Events\CheckoutFailed;
use Bazar\Events\CheckoutProcessing;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Transaction;
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
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     */
    abstract public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction;

    /**
     * Process the refund.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     */
    abstract public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction;

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    public function getName(): string
    {
        return preg_replace(
            '/([a-z0-9])([A-Z])/', '$1 $2', str_replace('Driver', '', class_basename(static::class))
        );
    }

    /**
     * Get the URL of the transaction.
     *
     * @param  \Bazar\Models\Transaction  $transaction
     * @return string|null
     */
    public function getTransactionUrl(Transaction $transaction): ?string
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
