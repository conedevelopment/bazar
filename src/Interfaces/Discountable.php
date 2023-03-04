<?php

namespace Cone\Bazar\Interfaces;

interface Discountable
{
    /**
     * Get the discount.
     */
    public function getDiscount(): float;

    /**
     * Get the formatted discount.
     */
    public function getFormattedDiscount(): string;

    /**
     * Calculate the discount.
     */
    public function calculateDiscount(bool $update = true): float;
}
