<?php

namespace Bazar\Listeners;

use Bazar\Events\CheckoutFailed;

class HandleFailedCheckout
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CheckoutFailing  $event
     * @return void
     */
    public function handle(CheckoutFailed $event): void
    {
        $event->order->status('failed');
    }
}
