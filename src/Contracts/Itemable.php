<?php

namespace Bazar\Contracts;

use Bazar\Contracts\Models\Product;
use Bazar\Contracts\Models\Shipping;
use Bazar\Models\Item;
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
     * Get the shipping attribute.
     *
     * @return \Bazar\Contracts\Models\Shipping
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
     * Get an item by its parent product and properties.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  array  $properties
     * @return \Bazar\Models\Item|null
     */
    public function item(Product $product, array $properties = []): ?Item;
}
