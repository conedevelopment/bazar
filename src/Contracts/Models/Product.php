<?php

namespace Bazar\Contracts\Models;

use Bazar\Models\Variant;
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
    public function variant(array $option): ?Variant;
}
