<?php

namespace Cone\Bazar\Interfaces\Repositories;

use Cone\Bazar\Interfaces\Discountable;

interface DiscountRepository
{
    /**
     * Register a new discount.
     *
     * @param  int|callable  $discount
     */
    public function register(string $name, $discount): void;

    /**
     * Disable the discount calculation.
     */
    public function disable(): void;

    /**
     * Enable the discount calculation.
     */
    public function enable(): void;

    /**
     * Calculate the total of the processed discounts.
     */
    public function calculate(Discountable $model): float;
}
