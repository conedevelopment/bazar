<?php

namespace Bazar\Gateway;

use Bazar\Events\CheckoutFailed;
use Bazar\Events\CheckoutProcessing;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Bazar\Support\Driver as BaseDriver;
use Illuminate\Http\Request;
use Throwable;

abstract class Driver extends BaseDriver
{
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
