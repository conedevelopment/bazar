<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Stockable;
use Bazar\Models\Variant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

interface Product extends Breadcrumbable, Stockable
{
    /**
     * Get the items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany;

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
     * Get the variants attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getVariantsAttribute(): Collection;

    /**
     * Get the variant of the given option.
     *
     * @param  array  $option
     * @return \Bazar\Models\Variant|null
     */
    public function toVariant(array $option): ?Variant;
}
