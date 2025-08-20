<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Interfaces\Stockable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Variant extends Buyable, Stockable
{
    /**
     * Get the items for the variant.
     */
    public function items(): MorphMany;

    /**
     * Get the orders for the variant.
     */
    public function orders(): HasManyThrough;

    /**
     * Get the carts for the variant.
     */
    public function carts(): HasManyThrough;

    /**
     * Get the product for the variant.
     */
    public function product(): BelongsTo;
}
