<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Models\Discount;

interface DiscountRate
{
    /**
     * Determine wheter the discount rate is applicable on the model.
     */
    public function applicable(Discountable $model): bool;

    /**
     * Apply the discount rate on the model.
     */
    public function apply(Discountable $model): ?Discount;
}
