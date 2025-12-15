<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Checkoutable;

interface Coupon
{
    /**
     * Validate the coupon for the checkoutable model.
     */
    public function validate(Checkoutable $model): bool;

    /**
     * Calculate the discount amount for the checkoutable model.
     */
    public function calculate(Checkoutable $model): float;

    /**
     * Apply the coupon to the checkoutable model.
     */
    public function apply(Checkoutable $model): void;
}
