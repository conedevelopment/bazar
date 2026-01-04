<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Tax;
use Cone\Bazar\Models\TaxRate;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

trait InteractsWithTaxes
{
    /**
     * Boot the trait.
     */
    public static function bootInteractsWithTaxes(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model)) || $model->forceDeleting) {
                $model->taxes()->detach();
            }
        });
    }

    /**
     * Get the taxes for the model.
     */
    public function taxes(): MorphToMany
    {
        return $this->morphToMany(TaxRate::getProxiedClass(), 'taxable', 'bazar_taxes')
            ->as('tax')
            ->using(Tax::getProxiedClass())
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * Get the applied tax rates.
     */
    public function getAppliedTaxRates(): Collection
    {
        return $this->taxes;
    }

    /**
     * Get the tax total.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function tax(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getTax()
        );
    }

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
     * Get the tax total.
     */
    public function getTax(): float
    {
        return $this->taxes->sum('tax.value');
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
        return $this->taxes->sum('tax.value') * $this->getQuantity();
    }
}
