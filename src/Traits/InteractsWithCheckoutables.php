<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait InteractsWithCheckoutables
{
    /**
     * Get the items for the product.
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::getProxiedClass(), 'buyable');
    }

    /**
     * Get the products for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\Cone\Bazar\Models\Order>
     */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::getProxiedClass(), Item::getProxiedClass(), 'buyable_id', 'id', 'id', 'checkoutable_id')
            ->where('checkoutable_type', Order::getProxiedClass())
            ->where('buyable_type', static::class);
    }

    /**
     * Get the carts for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\Cone\Bazar\Models\Cart>
     */
    public function carts(): HasManyThrough
    {
        return $this->hasManyThrough(Cart::getProxiedClass(), Item::getProxiedClass(), 'buyable_id', 'id', 'id', 'checkoutable_id')
            ->where('checkoutable_type', Cart::getProxiedClass())
            ->where('buyable_type', static::class);
    }
}
