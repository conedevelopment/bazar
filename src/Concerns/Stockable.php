<?php

namespace Bazar\Concerns;

use Bazar\Bazar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait Stockable
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
    public function price(string $type = 'normal', string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::currency();

        return $this->prices[$currency][$type] ?? null;
    }

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function formattedPrice(string $type = 'normal', string $currency = null): ?string
    {
        $currency = $currency ?: Bazar::currency();

        $price = $this->price($type, $currency);

        return $price ? Str::currency($price, $currency) : null;
    }

    /**
     * Get the formatted dimensions.
     *
     * @param  string  $glue
     * @return string|null
     */
    public function formattedDimensions(string $glue = 'x'): ?string
    {
        if (! $dimensions = array_filter($this->inventory('dimensions'))) {
            return null;
        }

        return sprintf('%s %s', implode($glue, $dimensions), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
     *
     * @return string|null
     */
    public function formattedWeight(): ?string
    {
        if (! $weight = $this->inventory('weight')) {
            return null;
        }

        return sprintf('%s %s', $weight, Config::get('bazar.weight_unit'));
    }

    /**
     * Get the value from the inventory array of the given attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $default
     * @return mixed
     */
    public function inventory(string $attribute, $default = null)
    {
        return Arr::get($this->inventory, $attribute, $default);
    }

    /**
     * Determine if the stockable model is free.
     *
     * @return bool
     */
    public function free(): bool
    {
        return is_null($this->price) || (int) $this->price === 0;
    }

    /**
     * Determine if the stockable model is on sale.
     *
     * @return bool
     */
    public function onSale(): bool
    {
        $price = $this->price('sale');

        return ! is_null($price) && (int) $price < (int) $this->price;
    }

    /**
     * Determine if the stockable model tracks quantity.
     *
     * @return bool
     */
    public function tracksQuantity(): bool
    {
        return ! is_null($this->inventory['quantity']);
    }

    /**
     * Determine if the stockable model is available.
     *
     * @param  float  $quantity
     * @return bool
     */
    public function available(float $quantity = 1): bool
    {
        $stock = $this->inventory['quantity'];

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
            $this->inventory = array_replace($this->inventory, [
                'quantity' => $this->inventory('quantity') + $quantity,
            ]);

            $this->save();
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
            $stock = $this->inventory('quantity');

            $this->inventory = array_replace($this->inventory, [
                'quantity' => max($stock - $quantity, 0),
            ]);

            $this->save();
        }
    }
}
