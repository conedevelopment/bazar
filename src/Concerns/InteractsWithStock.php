<?php

namespace Bazar\Concerns;

use Bazar\Bazar;

trait InteractsWithStock
{
    /**
     * Get the price attribute.
     *
     * @return float|null
     */
    public function getPriceAttribute(): ?float
    {
        return $this->price();
    }

    /**
     * Get the formatted price attribute.
     *
     * @return string|null
     */
    public function getFormattedPriceAttribute(): ?string
    {
        return $this->formattedPrice();
    }

    /**
     * Get the price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function price(string $type = 'default', string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::currency();

        return $this->prices[$currency][$type];
    }

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function formattedPrice(string $type = 'default', string $currency = null): ?string
    {
        $currency = $currency ?: Bazar::currency();

        $price = $this->prices[$currency];

        return $price ? $price->format($type) : null;
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
        $price = $this->price('sale');

        return ! is_null($price) && $price < $this->price;
    }
}
