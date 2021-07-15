<?php

namespace Cone\Bazar\Concerns;

use Cone\Bazar\Support\Facades\Discount;
use Illuminate\Support\Str;

trait InteractsWithDiscounts
{
    /**
     * Get the formatted discount attribute.
     *
     * @return string
     */
    public function getFormattedDiscountAttribute(): string
    {
        return $this->getFormattedDiscount();
    }

    /**
     * Get the discount.
     *
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * Get the formatted discount.
     *
     * @return string
     */
    public function getFormattedDiscount(): string
    {
        return Str::currency($this->getDiscount(), $this->currency);
    }

    /**
     * Calculate the discount.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateDiscount(bool $update = true): float
    {
        $this->discount = Discount::calculate($this);

        if ($update) {
            $this->save();
        }

        return $this->discount;
    }
}
