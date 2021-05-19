<?php

namespace Bazar\Listeners;

use Bazar\Events\CheckoutProcessed;
use Bazar\Jobs\SendNewOrderNotifications;
use Bazar\Support\Facades\Cart;

class PlaceOrder
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CheckoutProcessed  $event
     * @return void
     */
    public function handle(CheckoutProcessed $event): void
    {
        $event->order->markAs('in_progress');

        SendNewOrderNotifications::dispatch($event->order);

        Cart::empty();
    }
}
