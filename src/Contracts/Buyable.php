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
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return float
     */
    public function getBuyablePrice(Itemable $itemable, array $properties = []): float;

    /**
     * Get the buyable name.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return string
     */
    public function getBuyableName(Itemable $itemable, array $properties = []): string;

    /**
     * Get the buyable quantity.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return float|null
     */
    public function getBuyableQuantity(Itemable $itemable, array $properties = []): ?float;
}
