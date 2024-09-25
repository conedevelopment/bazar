<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\TaxRateFactory;
use Cone\Bazar\Interfaces\Models\TaxRate as Contract;
use Cone\Bazar\Interfaces\Taxable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_tax_rates';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TaxRateFactory
    {
        return TaxRateFactory::new();
    }

    /**
     * Calculate the tax for the taxable model.
     */
    public function calculate(Taxable $taxable): Tax
    {
        $value = round($taxable->getTaxBase() * $this->rate, 2);

        return $taxable->taxes()->updateOrCreate(
            ['tax_rate_id' => $this->getKey()],
            ['value' => $value]
        );
    }

    /**
     * Scope the query for the results that are applicable for shipping.
     */
    public function scopeApplicableForShipping(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('shipping'), true);
    }
}
