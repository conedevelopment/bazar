<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Jobs\SendNewOrderNotifications;
use Cone\Bazar\Support\Facades\Cart;

class PlaceOrder
{
    /**
     * Handle the event.
     *
     * @param  \Cone\Bazar\Events\CheckoutProcessed  $event
     * @return void
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
