<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessing;

class HandleProcessingCheckout
{
    /**
     * Handle the event.
     */
    public function handle(CheckoutProcessing $event): void
    {
        $event->order->markAs('pending');
    }
}
