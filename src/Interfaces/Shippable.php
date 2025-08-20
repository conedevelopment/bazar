<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Shippable
{
    /**
     * Get the shipping for the model.
     */
    public function shipping(): MorphOne;

    /**
     * Determine if the model needs shipping.
     */
    public function needsShipping(): bool;
}
