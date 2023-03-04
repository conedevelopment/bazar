<?php

namespace Cone\Bazar\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Shippable
{
    /**
     * Get the shipping for the model.
     */
    public function shipping(): MorphOne;
}
