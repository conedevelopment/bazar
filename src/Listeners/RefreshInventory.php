<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\PaymentCaptured;
use Cone\Bazar\Interfaces\Stockable;
use Cone\Bazar\Models\Item;

class RefreshInventory
{
    /**
     * Handle the event.
     */
    public function handle(PaymentCaptured $event): void
    {
        $event->order->loadMissing(['items', 'items.buyable']);

        $event->order->items->each(static function (Item $item): void {
            if (! $item->isFee() && $item->buyable instanceof Stockable && $item->buyable->tracksQuantity()) {
                $item->buyable->decrementQuantity($item->quantity);
            }
        });
    }
}
