<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\TaxRate;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasApplicableTaxRatesAsBuyable
{
    /**
     * Get the tax rates for the product.
     */
    public function taxRates(): MorphToMany
    {
        return $this->morphToMany(TaxRate::getProxiedClass(), 'buyable', 'bazar_buyable_tax_rate');
    }

    /**
     * Get the applicable tax rates for the buyable instance.
     */
    public function getApplicableTaxRates(): Collection
    {
        return $this->taxRates;
    }
}
