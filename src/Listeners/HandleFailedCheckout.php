<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutFailed;

class HandleFailedCheckout
{
    /**
     * Handle the event.
     */
    public function handle(CheckoutFailed $event): void
    {
        $event->order->markAs('failed');
    }
}
