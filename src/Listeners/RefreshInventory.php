<?php

namespace Bazar\Listeners;

use Bazar\Contracts\Models\Product;
use Bazar\Events\OrderPlaced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

        $event->order->products->reject(function (Product $product): bool {
            $variation = $product->variation($product->item->option);

            if ($shouldReject = ($variation && $variation->tracksQuantity())) {
                $variation->decrementQuantity($product->item->quantity);
            }

            return $shouldReject;
        })->groupBy(function (Product $product): string {
            return get_class($product).':'.$product->id;
        })->each(function (Collection $products): void {
            $products->first()->decrementQuantity($products->sum('item.quantity'));
        });
    }
}
