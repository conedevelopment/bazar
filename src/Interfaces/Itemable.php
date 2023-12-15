<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Itemable extends Shippable
{
    /**
     * Get the user for the model.
     */
    public function user(): BelongsTo;

    /**
     * Get the items for the model.
     */
    public function items(): MorphMany;

    /**
     * Get the currency.
     */
    public function getCurrency(): string;

    /**
     * Get the total.
     */
    public function getTotal(): float;

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string;

    /**
     * Get the subtotal.
     */
    public function getSubtotal(): float;

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotal(): string;

    /**
     * Get the itemable model's fee total.
     */
    public function getFeeTotal(): float;

    /**
     * Get the formatted fee total.
     */
    public function getFormattedFeeTotal(): string;

    /**
     * Get the tax.
     */
    public function getTax(): float;

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string;

    /**
     * Calculate the tax.
     */
    public function calculateTax(bool $update = true): float;

    /**
     * Find an item by its attributes or make a new instance.
     */
    public function findItem(array $attributes): ?Item;

    /**
     * Merge the given item into the collection.
     *
     * @param  \Cone\Bazar\Models\Item  $items
     */
    public function mergeItem(Item $item): Item;

    /**
     * Sync the items.
     */
    public function syncItems(): void;
}
