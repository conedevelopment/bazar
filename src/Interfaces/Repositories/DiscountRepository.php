<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Repositories;

use Closure;
use Cone\Bazar\Interfaces\Discount;
use Cone\Bazar\Interfaces\Discountable;

interface DiscountRepository
{
    /**
     * Register a new discount.
     */
    public function register(string $name, int|float|Closure|Discount $discount): void;

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
