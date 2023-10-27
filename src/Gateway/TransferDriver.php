<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Illuminate\Http\Request;
use Throwable;

class TransferDriver extends Driver
{
    /**
     * Process the payment.
     */
    public function pay(Order $order, float $amount = null): Transaction
    {
        return $order->pay($amount, 'transfer');
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, float $amount = null): Transaction
    {
        return $order->refund($amount, 'transfer');
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

        $url = $order->status === Order::PENDING
            ? $this->config['success_url']
            : $this->config['failed_url'];

        return parent::checkout($request, $order)->url($url);
    }
}
