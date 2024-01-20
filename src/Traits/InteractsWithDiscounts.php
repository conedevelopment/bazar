<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Support\Facades\Discount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Number;

trait InteractsWithDiscounts
{
    /**
     * Get the formatted discount attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedDiscount(): Attribute
    {
        return new Attribute(
            get: function (): string {
                return $this->getFormattedDiscount();
            }
        );
    }

    /**
     * Get the discount.
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * Get the formatted discount.
     */
    public function getFormattedDiscount(): string
    {
        return Number::currency($this->getDiscount(), $this->currency);
    }

    /**
     * Calculate the discount.
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
