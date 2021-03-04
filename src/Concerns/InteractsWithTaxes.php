<?php

namespace Bazar\Concerns;

use Bazar\Bazar;
use Bazar\Contracts\Models\Shipping;
use Bazar\Models\Item;
use Bazar\Support\Facades\Tax;
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
        return $this->formattedTax();
    }

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function formattedTax(): string
    {
        $currency = Bazar::currency();

        if ($this instanceof Item) {
            $currency = $this->itemable->currency;
        }

        if ($this instanceof Shipping) {
            $currency = $this->shippable->currency;
        }

        return Str::currency($this->tax, $currency);
    }

    /**
     * Calculate the tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function tax(bool $update = true): float
    {
        $tax = Tax::calculate($this);

        if ($this->exists && $update) {
            $this->update(['tax' => $tax]);
        }

        return $this->tax = $tax;
    }
}
