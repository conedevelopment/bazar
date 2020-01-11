<?php

namespace Bazar\Listeners;

use Bazar\Events\CartTouched;

class RefreshCart
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CartTouched  $event
     * @return void
     */
    public function handle(CartTouched $event): void
    {
        $event->cart->shipping->cost(false);
        $event->cart->shipping->tax(false);
        $event->cart->shipping->save();

        $event->cart->discount(false);
        $event->cart->save();
        $event->cart->refresh();
    }
}
