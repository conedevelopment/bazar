<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Interfaces\Stockable;
use Cone\Bazar\Models\Variant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Product extends Buyable, Stockable
{
    /**
     * Get the items for the product.
     */
    public function items(): MorphMany;

    /**
     * Get the orders for the product.
     */
    public function orders(): HasManyThrough;

    /**
     * Get the carts for the product.
     */
    public function carts(): HasManyThrough;

    /**
     * Get the categories for the product.
     */
    public function categories(): BelongsToMany;

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany;

    /**
     * Get the tax rates for the product.
     */
    public function taxRates(): MorphToMany;

    /**
     * Get the variant of the given option.
     */
    public function toVariant(array $option): ?Variant;
}
