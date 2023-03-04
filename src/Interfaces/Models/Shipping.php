<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\LineItem;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Shipping extends LineItem
{
    /**
     * Get the shippable model for the shipping.
     */
    public function shippable(): MorphTo;

    /**
     * Calculate the cost.
     */
    public function calculateCost(bool $update = true): float;
}
