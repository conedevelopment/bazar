<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Buyable;
use Bazar\Contracts\Stockable;
use Bazar\Models\Variant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

interface Product extends Buyable, Breadcrumbable, Stockable
{
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
