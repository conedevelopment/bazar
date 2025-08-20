<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

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
    public function calculateDiscount(bool $update = true): float
    {
        $this->discount = Discount::calculate($this);

        if ($update) {
            $this->save();
        }

        return $this->discount;
    }
}
