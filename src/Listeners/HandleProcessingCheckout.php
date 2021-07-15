<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessing;

class HandleProcessingCheckout
{
    /**
     * Handle the event.
     *
     * @param  \Cone\Bazar\Events\CheckoutProcessing  $event
     * @return void
     */
    public function handle(CheckoutProcessing $event): void
    {
        $event->order->markAs('pending');
    }
}
