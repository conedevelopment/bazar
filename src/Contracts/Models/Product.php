<?php

namespace Bazar\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

interface Product
{
    /**
     * Get the orders for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function orders(): MorphToMany;

    /**
     * Get the carts for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function carts(): MorphToMany;

    /**
     * Get all of the categories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany;

    /**
     * Get the variations for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variations(): HasMany;

    /**
     * Get the variations attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getVariationsAttribute(): Collection;

    /**
     * Get the variation of the given option.
     *
     * @param  array  $option
     * @return \Bazar\Models\Variation|null
     */
    public function variation(array $option): ?Variation;
}
