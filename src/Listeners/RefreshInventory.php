<?php

namespace Cone\Bazar\Listeners;

use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Interfaces\Stockable;
use Cone\Bazar\Models\Item;

class RefreshInventory
{
    /**
     * Handle the event.
     */
    public function handle(CheckoutProcessed $event): void
    {
        $event->order->loadMissing(['items', 'items.buyable']);

        $event->order->items->each(static function (Item $item): void {
            if (($model = $item->buyable) instanceof Stockable && $model->tracksQuantity()) {
                $model->decrementQuantity($item->quantity);

                $model->save();
            }
        });
    }
}
