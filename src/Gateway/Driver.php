<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Events\CheckoutFailed;
use Cone\Bazar\Events\CheckoutProcessing;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Driver as BaseDriver;
use Illuminate\Http\Request;
use Throwable;

abstract class Driver extends BaseDriver
{
    /**
     * Process the payment.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Cone\Bazar\Models\Transaction
     */
    abstract public function pay(Order $order, ?float $amount = null): Transaction;

    /**
     * Process the refund.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Cone\Bazar\Models\Transaction
     */
    abstract public function refund(Order $order, ?float $amount = null): Transaction;

    /**
     * Get the URL of the transaction.
     *
     * @param  \Cone\Bazar\Models\Transaction  $transaction
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
     * @param  \Cone\Bazar\Models\Cart  $cart
     * @return \Cone\Bazar\Models\Order
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
