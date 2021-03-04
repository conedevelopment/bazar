<?php

namespace Bazar\Concerns;

use Bazar\Support\Facades\Discount;
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
        return $this->formattedDiscount();
    }

    /**
     * Get the formatted discount.
     *
     * @return string
     */
    public function formattedDiscount(): string
    {
        return Str::currency($this->discount, $this->currency);
    }

    /**
     * Calculate the discount.
     *
     * @param  bool  $update
     * @return float
     */
    public function discount(bool $update = true): float
    {
        $discount = Discount::calculate($this);

        if ($this->exists && $update) {
            $this->update(['discount' => $discount]);
        }

        return $this->discount = $discount;
    }
}
