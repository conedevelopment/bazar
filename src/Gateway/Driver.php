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
     */
    abstract public function pay(Order $order, float $amount = null): Transaction;

    /**
     * Process the refund.
     */
    abstract public function refund(Order $order, float $amount = null): Transaction;

    /**
     * Get the URL of the transaction.
     */
    public function getTransactionUrl(Transaction $transaction): ?string
    {
        return null;
    }

    /**
     * Create a new payment transaction for the order.
     */
    public function createPayment(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        return $order->pay($amount, $this->key, $attributes);
    }

    /**
     * Create a new refund transaction for the order.
     */
    public function createRefund(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        return $order->refund($amount, $this->key, $attributes);
    }

    /**
     * Handle the checkout request.
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
