<?php

namespace Bazar\Listeners;

use Bazar\Contracts\Stockable;
use Bazar\Events\CheckoutProcessed;
use Bazar\Models\Item;

class RefreshInventory
{
    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\CheckoutProcessed  $event
     * @return void
     */
    public function handle(CheckoutProcessed $event): void
    {
        $event->order->loadMissing(['items', 'items.buyable']);

        $event->order->items->each(static function (Item $item): void {
            if (($model = $item->buyable) instanceof Stockable && $model->inventory->tracksQuantity()) {
                $model->inventory->decrementQuantity($item->quantity);

                $model->save();
            }
        });
    }
}
