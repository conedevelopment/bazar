<?php

namespace Bazar\Listeners;

use Bazar\Events\CheckoutFailing;

class HandleFailingCheckout
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CheckoutFailing  $event
     * @return void
     */
    public function handle(CheckoutFailing $event): void
    {
        $event->order->status('failed');
    }
}
