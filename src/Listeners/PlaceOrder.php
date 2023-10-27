<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Jobs\SendNewOrderNotifications;
use Cone\Bazar\Models\Order;

class PlaceOrder
{
    /**
     * Handle the event.
     */
    public function handle(CheckoutProcessed $event): void
    {
        $event->order->markAs(Order::IN_PROGRESS);

        SendNewOrderNotifications::dispatch($event->order);

        if ($event->order->cart) {
            $event->order->cart->delete();
        }
    }
}
