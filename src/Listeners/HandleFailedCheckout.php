<?php

namespace Bazar\Listeners;

use Bazar\Events\CheckoutFailed;

class HandleFailedCheckout
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CheckoutFailed  $event
     * @return void
     */
    public function handle(CheckoutFailed $event): void
    {
        $event->order->markAs('failed');
    }
}
