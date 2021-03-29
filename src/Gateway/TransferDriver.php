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
     * @return \Bazar\Models\Transaction
     */
    public function pay(Order $order, float $amount = null): Transaction
    {
        return tap($this->transaction($order, 'payment', $amount), static function (Transaction $transaction): void {
            $transaction->save();
        });
    }

    /**
     * Process the refund.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  float|null  $amount
     * @return \Bazar\Models\Transaction
     */
    public function refund(Order $order, float $amount = null): Transaction
    {
        return tap($this->transaction($order, 'refund', $amount), static function (Transaction $transaction): void {
            $transaction->save();
        });
    }
}
