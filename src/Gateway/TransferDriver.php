<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;

class TransferDriver extends Driver
{
    /**
     * The driver key.
     */
    protected string $key = 'transfer';

    /**
     * Process the payment.
     */
    public function pay(Order $order, float $amount = null): Transaction
    {
        return $this->createPayment($order, $amount);
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, float $amount = null): Transaction
    {
        return $this->createRefund($order, $amount);
    }
}
