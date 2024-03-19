<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransferDriver extends Driver
{
    /**
     * The driver name.
     */
    protected string $name = 'transfer';

    /**
     * {@inheritdoc}
     */
    public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return parent::pay($order, $amount, array_merge(['key' => Str::random()], $attributes));
    }

    /**
     * {@inheritdoc}
     */
    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return parent::refund($order, $amount, array_merge(['key' => Str::random()], $attributes));
    }

    /**
     * {@inheritdoc}
     */
    public function handleNotification(Request $request): Response
    {
        throw new Exception('This payment gateway does not support payment notifications.');
    }
}
