<?php

namespace Cone\Bazar\Interfaces;

interface Discountable
{
    /**
     * Get the discount.
     *
     * @return float
     */
    public function getDiscount(): float;

    /**
     * Get the formatted discount.
     *
     * @return string
     */
    public function getFormattedDiscount(): string;

    /**
     * Calculate the discount.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateDiscount(bool $update = true): float;
}
