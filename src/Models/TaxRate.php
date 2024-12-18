<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\TaxRateFactory;
use Cone\Bazar\Interfaces\Models\TaxRate as Contract;
use Cone\Bazar\Interfaces\Taxable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class TaxRate extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'formatted_value',
        'rate',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'shipping' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipping' => 'bool',
        'value' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'shipping',
        'value',
    ];

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
     * Get the rate attribute.
     */
    protected function rate(): Attribute
    {
        return new Attribute(
            get: fn (): float => round($this->value / 100, 2)
        );
    }

    /**
     * Get the formatted value attribute.
     */
    protected function formattedValue(): Attribute
    {
        return new Attribute(
            get: fn (): string => Number::percentage($this->value, 2)
        );
    }

    /**
     * Calculate the tax for the taxable model.
     */
    public function calculate(Taxable $taxable): float
    {
        return round($taxable->getTaxBase() * $this->rate, 2);
    }

    /**
     * Scope the query for the results that are applicable for shipping.
     */
    public function scopeApplicableForShipping(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('shipping'), true);
    }
}
