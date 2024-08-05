<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class ManualDriver extends Driver
{
    /**
     * The driver name.
     */
    protected string $name = 'manual';

    /**
     * Process the payment.
     */
    public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        $transaction = parent::pay($order, $amount, $attributes);

        $transaction->markAsCompleted();

        return $transaction;
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        $transaction = parent::refund($order, $amount, $attributes);

        $transaction->markAsCompleted();

        return $transaction;
    }

    /**
     * Handle the notification request.
     */
    public function handleNotification(Request $request, Order $order): Response
    {
        throw new Exception('This payment gateway does not support payment notifications.');
    }
}
