<?php

declare(strict_types=1);

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
     * Get the discount rate.
     */
    public function getDiscountRate(): float;

    /**
     * Get the formatted discount rate.
     */
    public function getFormattedDiscountRate(): string;

    /**
     * Calculate the discount.
     */
    public function calculateDiscount(): float;
}
