<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;

class TransferDriver extends Driver
{
    /**
     * Process the payment.
     */
    public function pay(Order $order, float $amount = null): Transaction
    {
        return $order->pay($amount, 'transfer');
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, float $amount = null): Transaction
    {
        return $order->refund($amount, 'transfer');
    }
}
