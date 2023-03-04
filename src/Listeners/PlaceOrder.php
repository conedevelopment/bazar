<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Jobs\SendNewOrderNotifications;

class PlaceOrder
{
    /**
     * Handle the event.
     */
    public function handle(CheckoutProcessed $event): void
    {
        $event->order->markAs('in_progress');

        SendNewOrderNotifications::dispatch($event->order);

        if ($event->order->cart) {
            $event->order->cart->delete();
        }
    }
}
