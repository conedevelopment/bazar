<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Tax;
use Illuminate\Support\Str;

trait InteractsWithTaxes
{
    /**
     * Get the formatted tax attribute.
     */
    public function getFormattedTaxAttribute(): string
    {
        return $this->getFormattedTax();
    }

    /**
     * Get the tax.
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string
    {
        $currency = Bazar::getCurrency();

        if ($this instanceof Item) {
            $currency = $this->itemable->currency;
        }

        if ($this instanceof Shipping) {
            $currency = $this->shippable->currency;
        }

        return Str::currency($this->getTax(), $currency);
    }

    /**
     * Calculate the tax.
     */
    public function calculateTax(bool $update = true): float
    {
        $this->tax = Tax::calculate($this);

        if ($update) {
            $this->save();
        }

        return $this->tax;
    }
}
