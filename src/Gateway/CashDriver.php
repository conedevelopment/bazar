<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;

class CashDriver extends Driver
{
    /**
     * The driver name.
     */
    protected string $name = 'cash';

    /**
     * Process the payment.
     */
    public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return parent::pay($order, $amount, array_merge($attributes, [
            'completed_at' => time(),
        ]));
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return parent::refund($order, $amount, array_merge($attributes, [
            'completed_at' => time(),
        ]));
    }
}
