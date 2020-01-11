<?php

namespace Bazar\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Shippable
{
    /**
     * Get the shipping for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function shipping(): MorphOne;
}
