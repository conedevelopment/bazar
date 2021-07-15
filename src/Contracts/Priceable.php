<?php

namespace Cone\Bazar\Contracts;

interface Priceable
{
    /**
     * Get the price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function getPrice(string $type = 'default', ?string $currency = null): ?float;

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function getFormattedPrice(string $type = 'default', ?string $currency = null): ?string;

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
