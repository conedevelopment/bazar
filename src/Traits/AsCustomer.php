<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait AsCustomer
{
    /**
     * Get the carts for the user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::getProxiedClass());
    }

    /**
     * Get the active cart for the user.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::getProxiedClass())->latestOfMany();
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::getProxiedClass());
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::getProxiedClass(), 'addressable');
    }

    /**
     * Get the default address for the user.
     */
    public function address(): MorphOne
    {
        return $this->addresses()->one()->ofMany([
            'default' => 'max',
            'id' => 'min',
        ])->withDefault();
    }
}
