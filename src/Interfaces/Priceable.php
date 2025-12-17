<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Enums\Currency;

interface Priceable
{
    /**
     * Get the price by the given type and currency.
     */
    public function getPrice(?Currency $currency = null): ?float;

    /**
     * Get the formatted price by the given type and currency.
     */
    public function getFormattedPrice(?Currency $currency = null): ?string;

    /**
     * Determine if the stockable model is free.
     */
    public function isFree(): bool;
}
