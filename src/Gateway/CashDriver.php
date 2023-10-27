<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;

class CashDriver extends Driver
{
    /**
     * The driver key.
     */
    protected string $key = 'cash';

    /**
     * Process the payment.
     */
    public function pay(Order $order, float $amount = null): Transaction
    {
        $transaction = $this->createPayment($order, $amount, [
            'completed_at' => time(),
        ]);

        CheckoutProcessed::dispatch($order);

        return $transaction;
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, float $amount = null): Transaction
    {
        $transaction = $this->createRefund($order, $amount, [
            'completed_at' => time(),
        ]);

        $order->markAs('refunded');

        return $transaction;
    }
}
