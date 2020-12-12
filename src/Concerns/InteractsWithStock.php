<?php

namespace Bazar\Concerns;

use Bazar\Bazar;
use Bazar\Support\Bags\Inventory;
use Bazar\Support\Bags\Prices;

trait InteractsWithStock
{
    /**
     * Get the inventory attribute.
     *
     * @param  string  $value
     * @return \Bazar\Support\Bags\Inventory
     */
    public function getInventoryAttribute(string $value): Inventory
    {
        $value = $value ? json_decode($value, true) : [];

        return new Inventory($value);
    }

    /**
     * Set the inventory attribute.
     *
     * @param  array  $value
     * @return void
     */
    public function setInventoryAttribute(array $value): void
    {
        $this->attributes['inventory'] = json_encode($value, JSON_NUMERIC_CHECK);
    }

    /**
     * Get the prices attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function getPricesAttribute(string $value): Prices
    {
        $value = $value ? json_decode($value, true) : [];

        return new Prices($value);
    }

    /**
     * Set the prices attribute.
     *
     * @param  array  $value
     * @return void
     */
    public function setPricesAttribute(array $value): void
    {
        $this->attributes['prices'] = json_encode($value, JSON_NUMERIC_CHECK);
    }

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
