<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Models\Order;
use Illuminate\Http\Request;

class TransferDriver extends Driver
{
    /**
     * The driver name.
     */
    protected string $name = 'transfer';

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
