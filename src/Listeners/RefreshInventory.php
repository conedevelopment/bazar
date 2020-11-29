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

            if ($shouldReject = ($variation && $variation->inventory->tracksQuantity())) {
                $variation->inventory->decrementQuantity($product->item->quantity);

                $variation->save();
            }

            return $shouldReject;
        })->groupBy(function (Model $product): string {
            return get_class($product).':'.$product->id;
        })->each(function (Collection $products): void {
            tap($products->first(), static function (Model $product) use ($products): void {
                $product->inventory->decrementQuantity($products->sum('item.quantity'));
            })->save();
        });
    }
}
