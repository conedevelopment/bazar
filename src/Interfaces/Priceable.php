<?php

namespace Cone\Bazar\Interfaces;

interface Priceable
{
    /**
     * Get the price by the given type and currency.
     */
    public function getPrice(string $type = 'default', ?string $currency = null): ?float;

    /**
     * Get the formatted price by the given type and currency.
     */
    public function getFormattedPrice(string $type = 'default', ?string $currency = null): ?string;

    /**
     * Determine if the stockable model is free.
     */
    public function isFree(): bool;

    /**
     * Determine if the stockable model is on sale.
     */
    public function onSale(): bool;
}
