<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait AsCustomer
{
    /**
     * Get the carts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::getProxiedClass());
    }

    /**
     * Get the active cart for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::getProxiedClass())->latestOfMany();
    }

    /**
     * Get the orders for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::getProxiedClass());
    }

    /**
     * Get the addresses for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::getProxiedClass(), 'addressable');
    }

    /**
     * Get the address attribute.
     *
     * @return \Cone\Bazar\Models\Address|null
     */
    public function getAddressAttribute(): ?Address
    {
        return $this->addresses->firstWhere('default', true) ?: $this->addresses->first();
    }
}
