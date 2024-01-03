<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Models\Order;

class PlaceOrder
{
    /**
     * Handle the event.
     */
    public function handle(CheckoutProcessed $event): void
    {
        $event->order->markAs(Order::IN_PROGRESS);

        if (! is_null($event->order->cart)) {
            $event->order->cart->delete();
        }
    }
}
