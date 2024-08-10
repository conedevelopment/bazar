<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\DiscountRate;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Discount;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        return (new Currency($this->getDiscount(), $this->currency))->format();
    }

    /**
     * Calculate the discount.
     */
    public function calculateDiscount(): float
    {
        $total = 0;

        DiscountRate::query()->get()->each(function (DiscountRate $rate) use (&$total): void {
            $total += $rate->apply($this)?->value ?: 0;
        });

        return $total;
    }
}
