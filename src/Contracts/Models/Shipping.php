<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\LineItem;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Shipping extends LineItem
{
    /**
     * Get the shippable model for the shipping.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shippable(): MorphTo;

    /**
     * Calculate the cost.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateCost(bool $update = true): float;
}
