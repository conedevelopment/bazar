<?php

namespace Bazar\Gateway;

use Bazar\Models\Order;
use Bazar\Models\Transaction;

class CashDriver extends Driver
{
    /**
     * Process the payment.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Models\Transaction
     */
    public function pay(Order $order, ?float $amount = null): Transaction
    {
        return $this->transaction($order, 'payment', $amount)->markAsCompleted();
    }

    /**
     * Process the refund.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Models\Transaction
     */
    public function refund(Order $order, ?float $amount = null): Transaction
    {
        return $this->transaction($order, 'refund', $amount)->markAsCompleted();
    }
}
