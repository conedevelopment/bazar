<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Illuminate\Http\Request;

class TransferDriver extends Driver
{
    /**
     * Process the payment.
     */
    public function pay(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        return $order->pay($amount, 'transfer', $attributes);
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        return $order->refund($amount, 'transfer', $attributes);
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

        return $response->url($url);
    }
}
