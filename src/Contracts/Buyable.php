<?php

namespace Bazar\Contracts;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Buyable
{
    /**
     * Get the items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function items(): MorphMany;

    /**
     * Get the orders for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orders(): HasManyThrough;

    /**
     * Get the carts for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function carts(): HasManyThrough;

    /**
     * Get the buyable price.
     *
     * @return float
     */
    // public function getBuyablePrice(Itemable $model): float;

    /**
     * Get the buyable name.
     *
     * @return string
     */
    // public function getBuyableName(Itemable $model): string;

    /**
     * Get the buyable quantity.
     *
     * @return float|null
     */
    // public function getBuyableQuantity(Itemable $model): ?float;
}
