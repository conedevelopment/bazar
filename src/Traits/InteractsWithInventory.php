<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Illuminate\Support\Facades\Config;

trait InteractsWithInventory
{
    /**
     * Get the formatted dimensions.
     */
    public function getFormattedDimensions(): ?string
    {
        $dimensions = $this->metaData->whereIn('key', ['length', 'width', 'height'])->filter()->values();

        if ($dimensions->isEmpty()) {
            return null;
        }

        return sprintf('%s %s', $dimensions->implode('value', 'x'), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
     */
    public function getFormattedWeight(): ?string
    {
        $weight = $this->metaData->firstWhere('key', 'weight');

        if (is_null($weight) || empty($weight->value)) {
            return null;
        }

        return sprintf('%s %s', $weight->value, Config::get('bazar.weight_unit'));
    }

    /**
     * Determine if the model is virtual.
     */
    public function isVirtual(): bool
    {
        $meta = $this->metaData->firstWhere('key', 'virtual');

        return ! is_null($meta) && (bool) $meta->value;
    }

    /**
     * Determine if the model is physical.
     */
    public function isPhysical(): bool
    {
        return ! $this->isVirtual();
    }

    /**
     * Determine if the model is downloadable.
     */
    public function isDownloadable(): bool
    {
        $meta = $this->metaData->firstWhere('key', 'downloadable');

        return ! is_null($meta) && (bool) $meta->value;
    }

    /**
     * Determine if the model tracks quantity.
     */
    public function tracksQuantity(): bool
    {
        $meta = $this->metaData->firstWhere('key', 'quantity');

        return ! is_null($meta) && ! empty($meta->value);
    }

    /**
     * Determine if the model is available.
     */
    public function isAvailable(float $quantity = 1): bool
    {
        if (! $this->tracksQuantity()) {
            return true;
        }

        $stock = $this->metaData->firstWhere('key', 'quantity')?->value ?: 0;

        return min($stock, $quantity) > 0 && $stock >= $quantity;
    }

    /**
     * Increment the quantity by the given value.
     */
    public function incrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $meta = $this->metaData->firstWhere('key', 'quantity')
                ?: $this->metaData()->make(['key' => 'quantity', 'value' => 0]);

            $meta->value = ((float) $meta->value) + $quantity;

            $meta->save();
        }
    }

    /**
     * Decrement the quantity by the given value.
     */
    public function decrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $meta = $this->metaData->firstWhere('key', 'quantity')
                ?: $this->metaData()->make(['key' => 'quantity', 'value' => 0]);

            $meta->value = max(((float) $meta->value) - $quantity, 0);

            $meta->save();
        }
    }
}
