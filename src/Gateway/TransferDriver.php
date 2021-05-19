<?php

namespace Bazar\Gateway;

use Bazar\Models\Order;
use Bazar\Models\Transaction;

class TransferDriver extends Driver
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
        return $order->pay($amount, $this->id(), $attributes);
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
        return $order->refund($amount, $this->id(), $attributes);
    }
}
