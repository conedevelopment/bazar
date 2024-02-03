<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Tax;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait InteractsWithTaxes
{
    /**
     * Get the formatted tax attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedTax(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedTax()
        );
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

        return (new Currency($this->getTax(), $currency))->format();
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
