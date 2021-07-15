<?php

namespace Cone\Bazar\Concerns;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Tax;
use Illuminate\Support\Str;

trait InteractsWithTaxes
{
    /**
     * Get the formatted tax attribute.
     *
     * @return string
     */
    public function getFormattedTaxAttribute(): string
    {
        return $this->getFormattedTax();
    }

    /**
     * Get the tax.
     *
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * Get the formatted tax.
     *
     * @return string
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
     *
     * @param  bool  $update
     * @return float
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
