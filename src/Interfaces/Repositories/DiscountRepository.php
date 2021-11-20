<?php

namespace Cone\Bazar\Interfaces\Repositories;

use Cone\Bazar\Interfaces\Discountable;

interface DiscountRepository
{
    /**
     * Register a new discount.
     *
     * @param  string  $name
     * @param  int|callable  $discount
     * @return void
     */
    public function register(string $name, $discount): void;

    /**
     * Disable the discount calculation.
     *
     * @return void
     */
    public function disable(): void;

    /**
     * Enable the discount calculation.
     *
     * @return void
     */
    public function enable(): void;

    /**
     * Calculate the total of the processed discounts.
     *
     * @param  \Cone\Bazar\Interfaces\Discountable  $model
     * @return float
     */
    public function calculate(Discountable $model): float;
}
