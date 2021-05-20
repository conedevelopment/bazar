<?php

namespace Bazar\Contracts;

use Bazar\Models\Shipping;
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
     * @return \Bazar\Models\Shipping
     */
    public function getShippingAttribute(): Shipping;
}
