<?php

namespace Bazar\Gateway;

use Bazar\Contracts\Models\Order;
use Bazar\Contracts\Models\Transaction;

class CashDriver extends Driver
{
    /**
     * Process the payment.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Contracts\Models\Transaction
     */
    public function pay(Order $order, float $amount = null): Transaction
    {
        return $this->transaction($order, 'payment', $amount)->markAsCompleted();
    }

    /**
     * Process the refund.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Contracts\Models\Transaction
     */
    public function refund(Order $order, float $amount = null): Transaction
    {
        return $this->transaction($order, 'refund', $amount)->markAsCompleted();
    }
}
