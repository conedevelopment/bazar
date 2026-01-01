<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Discountable
{
    /**
     * Get the discounts for the model.
     */
    public function discounts(): MorphToMany;

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
