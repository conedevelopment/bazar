<?php

namespace Bazar\Contracts;

interface Discountable
{
    /**
     * Get the formatted discount attribute.
     *
     * @return string
     */
    public function getFormattedDiscountAttribute(): string;

    /**
     * Get the formatted discount.
     *
     * @return string
     */
    public function formattedDiscount(): string;

    /**
     * Calculate the discount.
     *
     * @param  bool  $update
     * @return float
     */
    public function discount(bool $update = true): float;
}
