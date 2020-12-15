<?php

namespace Bazar\Listeners;

use Bazar\Contracts\Stockable;
use Bazar\Events\OrderPlaced;
use Bazar\Models\Item;

class RefreshInventory
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event): void
    {
        $event->order->loadMissing(['products', 'products.variations']);

        $event->order->items->each(static function (Item $item): void {
            if (($model = $item->stockable) instanceof Stockable && $model->inventory->tracksQuantity()) {
                $model->inventory->decrementQuantity($item->quantity);

                $model->save();
            }
        });
    }
}
