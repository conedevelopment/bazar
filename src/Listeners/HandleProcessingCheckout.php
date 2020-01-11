<?php

namespace Bazar\Listeners;

use Bazar\Events\CheckoutProcessing;

class HandleProcessingCheckout
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CheckoutProcessing  $event
     * @return void
     */
    public function handle(CheckoutProcessing $event): void
    {
        $event->order->status('pending');
    }
}
