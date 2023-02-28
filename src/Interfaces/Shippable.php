<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Shipping;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Shippable
{
    /**
     * Get the shipping for the model.
     */
    public function shipping(): MorphOne;

    /**
     * Get the shipping attribute.
     */
    public function getShippingAttribute(): Shipping;
}
