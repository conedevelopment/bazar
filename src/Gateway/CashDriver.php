<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Illuminate\Http\Request;

class CashDriver extends Driver
{
    /**
     * Process the payment.
     */
    public function pay(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        $transaction = $order->pay($amount, 'cash', array_merge($attributes, [
            'completed_at' => time(),
        ]));

        $order->markAs(Order::PENDING);

        return $transaction;
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        $transaction = $order->refund($amount, 'cash', array_merge($attributes, [
            'completed_at' => time(),
        ]));

        $order->markAs(Order::REFUNDED);

        return $transaction;
    }

    /**
     * Handle the checkout request.
     */
    public function checkout(Request $request, Order $order): Response
    {
        $response = parent::checkout($request, $order);

        $url = $order->status === Order::PENDING
            ? $this->config['success_url']
            : $this->config['failed_url'];

        CheckoutProcessed::dispatch($order);

        return $response->url($url);
    }
}
