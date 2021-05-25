<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Taxable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Item extends Taxable
{
    /**
     * Get the buyable model for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function buyable(): MorphTo;

    /**
     * Get the itemable model for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function itemable(): MorphTo;

    /**
     * Get the formatted price attribute.
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string;

    /**
     * Get the total attribute.
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
     * Get the item's price.
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Get the item's formatted price.
     *
     * @return string
     */
    public function getFormattedPrice(): string;

    /**
     * Get the item's total.
     *
     * @return float
     */
    public function getTotal(): float;

    /**
     * Get the item's formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string;

    /**
     * Get the item's net total.
     *
     * @return float
     */
    public function getNetTotal(): float;

    /**
     * Get the item's formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string;
}
