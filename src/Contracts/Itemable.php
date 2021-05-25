<?php

namespace Bazar\Contracts;

use Bazar\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
     * Get the items for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function items(): MorphMany;

    /**
     * Get the currency.
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Get the total.
     *
     * @return float
     */
    public function getTotal(): float;

    /**
     * Get the formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string;

    /**
     * Get the net total.
     *
     * @return float
     */
    public function getNetTotal(): float;

    /**
     * Get the formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string;

    /**
     * Get the tax.
     *
     * @return float
     */
    public function getTax(): float;

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function getFormattedTax(): string;

    /**
     * Calculate the tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateTax(bool $update = true): float;

    /**
     * Get the downloadable files with their signed URL.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDownloads(): Collection;

    /**
     * Find an item by its attributes or make a new instance.
     *
     * @param  array  $attributes
     * @return \Bazar\Models\Item
     */
    public function findItemOrNew(array $attributes): Item;
}
