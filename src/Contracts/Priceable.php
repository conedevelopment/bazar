<?php

namespace Bazar\Contracts;

use Bazar\Support\Bags\Prices;

interface Priceable
{
    /**
     * Get the prices attribute.
     *
     * @param  string  $value
     * @return \Bazar\Support\Bags\Prices
     */
    public function getPricesAttribute(string $value): Prices;

    /**
     * Get the price attribute.
     *
     * @return float|null
     */
    public function getPriceAttribute(): ?float;

    /**
     * Get the formatted price attribute.
     *
     * @return string|null
     */
    public function getFormattedPriceAttribute(): ?string;

    /**
     * Get the price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function price(string $type = 'default', string $currency = null): ?float;

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function formattedPrice(string $type = 'default', string $currency = null): ?string;

    /**
     * Determine if the stockable model is free.
     *
     * @return bool
     */
    public function free(): bool;

    /**
     * Determine if the stockable model is on sale.
     *
     * @return bool
     */
    public function onSale(): bool;
}
