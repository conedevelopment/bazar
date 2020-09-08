<?php

namespace Bazar\Listeners;

use Bazar\Events\CheckoutProcessed;
use Bazar\Events\OrderPlaced;
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
        OrderPlaced::dispatch($event->order);

//        Cart::empty();
    }
}
