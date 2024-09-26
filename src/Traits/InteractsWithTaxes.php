<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Tax;
use Cone\Bazar\Models\TaxRate;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
