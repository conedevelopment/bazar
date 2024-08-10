<?php

namespace Cone\Bazar\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Discountable
{
    /**
     * Get the discounts.
     */
    public function discounts(): MorphMany;

    /**
     * Get the discount.
     */
    public function getTotalDiscount(): float;

    /**
     * Get the formatted discount.
     */
    public function getFormattedTotalDiscount(): string;

    /**
     * Calculate the discount.
     */
    public function calculateDiscount(): float;
}
