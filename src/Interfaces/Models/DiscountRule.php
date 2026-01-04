<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Discountable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface DiscountRule
{
    /**
     * Get the users associated with the discount rule.
     */
    public function users(): BelongsToMany;

    /**
     * Calculate the discount for the given discountable.
     */
    public function calculate(Discountable $discountable): float;

    /**
     * Apply the discount rule to the given discountable.
     */
    public function apply(Discountable $discountable): void;
}
