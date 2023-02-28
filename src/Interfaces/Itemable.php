<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

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
     * Get the net total.
     */
    public function getNetTotal(): float;

    /**
     * Get the formatted net total.
     */
    public function getFormattedNetTotal(): string;

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
     * Get the downloadable files with their signed URL.
     */
    public function getDownloads(): Collection;

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
