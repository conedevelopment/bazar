<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Driver as BaseDriver;
use Illuminate\Http\Request;
use Throwable;

abstract class Driver extends BaseDriver
{
    /**
     * The driver name.
     */
    protected string $name;

    /**
     * Process the payment.
     */
    public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return $order->pay($amount, $this->name, $attributes);
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return $order->refund($amount, $this->name, $attributes);
    }

    /**
     * Get the URL of the transaction.
     */
    public function getTransactionUrl(Transaction $transaction): ?string
    {
        return null;
    }

    /**
     * Handle the checkout request.
     */
    public function checkout(Request $request, Order $order): Response
    {
        try {
            $this->pay($order);
        } catch (Throwable $exception) {
            $order->markAs(Order::FAILED);
        }

        return new Response($order);
    }
}
