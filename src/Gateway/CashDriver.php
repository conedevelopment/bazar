<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;

class CashDriver extends Driver
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
        $transaction = $order->pay($amount, 'cash', [
            'completed_at' => time(),
        ]);

        CheckoutProcessed::dispatch($order);

        return $transaction;
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
        $transaction = $order->refund($amount, 'cash', [
            'completed_at' => time(),
        ]);

        $order->markAs('refunded');

        return $transaction;
    }
}
