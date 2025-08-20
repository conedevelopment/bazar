<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

interface Priceable
{
    /**
     * Get the price by the given type and currency.
     */
    public function getPrice(?string $currency = null): ?float;

    /**
     * Get the formatted price by the given type and currency.
     */
    public function getFormattedPrice(?string $currency = null): ?string;

    /**
     * Determine if the stockable model is free.
     */
    public function isFree(): bool;
}
