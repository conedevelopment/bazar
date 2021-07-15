<?php

namespace Cone\Bazar\Concerns;

use Cone\Bazar\Bazar;

trait InteractsWithStock
{
    /**
     * Get the price attribute.
     *
     * @return float|null
     */
    public function getPriceAttribute(): ?float
    {
        return $this->getPrice();
    }

    /**
     * Get the formatted price attribute.
     *
     * @return string|null
     */
    public function getFormattedPriceAttribute(): ?string
    {
        return $this->getFormattedPrice();
    }

    /**
     * Get the price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function getPrice(string $type = 'default', ?string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::getCurrency();

        return $this->prices->get("{$currency}.{$type}");
    }

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function getFormattedPrice(string $type = 'default', ?string $currency = null): ?string
    {
        $currency = $currency ?: Bazar::getCurrency();

        return $this->prices->format("{$currency}.{$type}");
    }

    /**
     * Determine if the stockable model is free.
     *
     * @return bool
     */
    public function free(): bool
    {
        return ! (bool) $this->price;
    }

    /**
     * Determine if the stockable model is on sale.
     *
     * @return bool
     */
    public function onSale(): bool
    {
        $price = $this->getPrice('sale');

        return ! is_null($price) && $price < $this->price;
    }
}
