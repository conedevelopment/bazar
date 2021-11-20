<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Shipping;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Shippable
{
    /**
     * Get the shipping for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function shipping(): MorphOne;

    /**
     * Get the shipping attribute.
     *
     * @return \Cone\Bazar\Models\Shipping
     */
    public function getShippingAttribute(): Shipping;
}
