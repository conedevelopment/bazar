<?php

namespace Bazar\Contracts;

use Bazar\Models\Item;
use Bazar\Models\Shipping;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

interface Itemable extends Shippable
{
    /**
     * Get the user for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo;

    /**
     * Get the products for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function products(): MorphToMany;

    /**
     * Get the shipping for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function shipping(): MorphOne;

    /**
     * Get the currency attribute.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getCurrencyAttribute(?string $value = null): string;

    /**
     * Get the shipping attribute.
     *
     * @return \Bazar\Models\Shipping
     */
    public function getShippingAttribute(): Shipping;

    /**
     * Get all the items of the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItemsAttribute(): Collection;

    /**
     * Get all the taxable items of the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTaxablesAttribute(): Collection;

    /**
     * Get the total attibute.
     *
     * @return float
     */
    public function getTotalAttribute(): float;

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string;

    /**
     * Get the net total attribute.
     *
     * @return float
     */
    public function getNetTotalAttribute(): float;

    /**
     * Get the formatted net total attribute.
     *
     * @return string
     */
    public function getFormattedNetTotalAttribute(): string;

    /**
     * Get the tax attribute.
     *
     * @return float
     */
    public function getTaxAttribute(): float;

    /**
     * Get the formatted tax attribute.
     *
     * @return string
     */
    public function getFormattedTaxAttribute(): string;

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function total(): float;

    /**
     * Get the formatted total.
     *
     * @return string
     */
    public function formattedTotal(): string;

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function netTotal(): float;

    /**
     * Get the formatted net total.
     *
     * @return string
     */
    public function formattedNetTotal(): string;

    /**
     * Get the total tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function tax(bool $update = true): float;

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function formattedTax(): string;

    /**
     * Get the downloadable files with their signed URL.
     *
     * @return \Illuminate\Support\Collection
     */
    public function downloads(): Collection;

    /**
     * Find an item by its attributes or make a new instance.
     *
     * @param  array  $attributes
     * @return \Bazar\Models\Item
     */
    public function findItemOrNew(array $attributes): Item;
}
