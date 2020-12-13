<?php

namespace Bazar\Concerns;

use Bazar\Bazar;
use Bazar\Casts\Bag as BagCast;
use Bazar\Support\Bags\Bag;
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
        if (isset($this->classCastCache['inventory'])) {
            return $this->classCastCache['inventory'];
        }

        $value = new Inventory(
            $value ? json_decode($value, true) : []
        );

        return $this->cacheBagCast('inventory', $value);
    }

    /**
     * Get the prices attribute.
     *
     * @param  string  $value
     * @return \Bazar\Support\Bags\Prices
     */
    public function getPricesAttribute(string $value): Prices
    {
        if (isset($this->classCastCache['prices'])) {
            return $this->classCastCache['prices'];
        }

        $value = new Prices(
            $value ? json_decode($value, true) : []
        );

        return $this->cacheBagCast('prices', $value);
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

    /**
     * Cache the casted attribute bag.
     *
     * @phpstan-template TBag of \Bazar\Support\Bags\Bag
     * @phpstan-param    string  $key
     * @phpstan-param    TBag  $value
     * @phpstan-return   TBag
     *
     * @param  string  $key
     * @param  \Bazar\Support\Bags\Bag  $value
     * @return \Bazar\Support\Bags\Bag
     */
    protected function cacheBagCast(string $key, Bag $value): Bag
    {
        if (! $this->hasCast($key, BagCast::class)) {
            $this->mergeCasts([$key => BagCast::class]);
        }

        return $this->classCastCache[$key] = $value;
    }
}
