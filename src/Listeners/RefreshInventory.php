<?php

namespace Bazar\Listeners;

use Bazar\Events\OrderPlaced;

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

        $event->order->products->reject(function ($product) {
            $variation = $product->variation($product->item->option);

            if ($shouldReject = ($variation && $variation->tracksQuantity())) {
                $variation->decrementQuantity($product->item->quantity);
            }

            return $shouldReject;
        })->groupBy(function ($product) {
            return get_class($product).':'.$product->id;
        })->each(function ($products) {
            $products->first()->decrementQuantity($products->sum('item.quantity'));
        });
    }
}
