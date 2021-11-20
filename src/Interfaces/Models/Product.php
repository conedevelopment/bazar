<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Interfaces\Stockable;
use Cone\Bazar\Models\Variant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Product extends Buyable, Stockable
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
     * Get the categories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany;

    /**
     * Get the variants for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants(): HasMany;

    /**
     * Get the variant of the given option.
     *
     * @param  array  $option
     * @return \Cone\Bazar\Models\Variant|null
     */
    public function toVariant(array $option): ?Variant;
}
