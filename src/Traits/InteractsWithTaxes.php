<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Tax;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait InteractsWithTaxes
{
    /**
     * Get the taxes for the model.
     */
    public function taxes(): MorphMany
    {
        return $this->morphMany(Tax::getProxiedClass(), 'taxable');
    }

    /**
     * Get the tax total.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function taxTotal(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getTaxTotal()
        );
    }

    /**
     * Get the formatted tax attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedTaxTotal(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedTaxTotal()
        );
    }

    /**
     * Get the tax total.
     */
    public function getTaxTotal(): float
    {
        return $this->taxes->sum('value');
    }
}
