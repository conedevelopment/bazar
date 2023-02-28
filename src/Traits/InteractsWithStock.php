<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

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
     * @param  string|null  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function getPrice(?string $type = null, ?string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::getCurrency();

        $key = sprintf('price%s_%s', is_null($type) ? '' : "_{$type}", $currency);

        $meta = $this->metas->firstWhere('key', $key);

        return is_null($meta) ? null : $meta->value;
    }

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string|null  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function getFormattedPrice(string $type = null, ?string $currency = null): ?string
    {
        $currency = $currency ?: Bazar::getCurrency();

        $price = $this->getPrice($type, $currency);

        return $price ? Str::currency($price, $currency) : null;
    }

    /**
     * Determine if the stockable model is free.
     *
     * @return bool
     */
    public function isFree(): bool
    {
        $price = $this->getPrice();

        return is_null($price) || $price === 0;
    }

    /**
     * Determine if the stockable model is on sale.
     *
     * @return bool
     */
    public function onSale(): bool
    {
        $price = $this->getPrice('sale');

        return ! is_null($price) && $price < $this->getPrice();
    }

    /**
     * Get the formatted dimensions.
     *
     * @param  string  $glue
     * @return string|null
     */
    public function getFormattedDimensions(string $glue = 'x'): ?string
    {
        $dimensions = array_filter($this->getAttributes(['metas.length', 'metas.width', 'metas.height']));

        if (empty($dimensions)) {
            return null;
        }

        return sprintf('%s %s', implode($glue, $dimensions), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
     *
     * @return string|null
     */
    public function getFormattedWeight(): ?string
    {
        $weight = $this->getAttribute('metas.weight');

        if (is_null($weight)) {
            return null;
        }

        return sprintf('%s %s', $weight, Config::get('bazar.weight_unit'));
    }

    /**
     * Determine if the stockable model is virtual.
     *
     * @return bool
     */
    public function isVirtual(): bool
    {
        return (bool) $this->getAttribute('metas.virtual');
    }

    /**
     * Determine if the stockable model is downloadable.
     *
     * @return bool
     */
    public function isDownloadable(): bool
    {
        return (bool) $this->getAttribute('metas.downloadable');
    }

    /**
     * Determine if the stockable model tracks quantity.
     *
     * @return bool
     */
    public function tracksQuantity(): bool
    {
        return ! is_null($this->getAttribute('metas.quantity'));
    }

    /**
     * Determine if the stockable model is available.
     *
     * @param  float  $quantity
     * @return bool
     */
    public function isAvailable(float $quantity = 1): bool
    {
        $stock = $this->getAttribute('metas.quantity') ?: 0;

        return ! $this->tracksQuantity() || (min($stock, $quantity) > 0 && $stock >= $quantity);
    }

    /**
     * Increment the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function incrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $value = $this->getAttribute('metas.quantity') ?: 0;

            $this->setAttribute('metas.quantity', $value + $quantity);
        }
    }

    /**
     * Decrement the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function decrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $value = $this->getAttribute('metas.quantity') ?: 0;

            $this->setAttribute('metas.quantity', max($value - $quantity, 0));
        }
    }
}
