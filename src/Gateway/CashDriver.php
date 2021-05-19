<?php

namespace Bazar\Gateway;

use Bazar\Events\CheckoutProcessed;
use Bazar\Models\Order;
use Bazar\Models\Transaction;

class CashDriver extends Driver
{
    /**
     * Process the payment.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     */
    public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        $transaction = $order->pay($amount, $this->id(), array_merge($attributes, [
            'completed_at' => time(),
        ]));

        CheckoutProcessed::dispatch($order);

        return $transaction;
    }

    /**
     * Process the refund.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     */
    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        $transaction = $order->refund($amount, $this->id(), array_merge($attributes, [
            'completed_at' => time(),
        ]));

        $order->markAs('refunded');

        return $transaction;
    }
}
