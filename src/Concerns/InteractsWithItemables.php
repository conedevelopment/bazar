<?php

namespace Bazar\Concerns;

use Bazar\Models\Cart;
use Bazar\Models\Item;
use Bazar\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait InteractsWithItemables
{
    /**
     * Get the items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::getProxiedClass(), 'buyable');
    }

    /**
     * Get the products for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::getProxiedClass(), Item::getProxiedClass(), 'buyable_id', 'id', 'id', 'itemable_id')
                    ->where('itemable_type', Order::getProxiedClass())
                    ->where('buyable_type', static::class);
    }

    /**
     * Get the carts for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function carts(): HasManyThrough
    {
        return $this->hasManyThrough(Cart::getProxiedClass(), Item::getProxiedClass(), 'buyable_id', 'id', 'id', 'itemable_id')
                    ->where('itemable_type', Cart::getProxiedClass())
                    ->where('buyable_type', static::class);;
    }
}
