<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;

class TransferDriver extends Driver
{
    /**
     * Process the payment.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Cone\Bazar\Models\Transaction
     */
    public function pay(Order $order, ?float $amount = null): Transaction
    {
        return $order->pay($amount, 'transfer');
    }

    /**
     * Process the refund.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Cone\Bazar\Models\Transaction
     */
    public function refund(Order $order, ?float $amount = null): Transaction
    {
        return $order->refund($amount, 'transfer');
    }
}
