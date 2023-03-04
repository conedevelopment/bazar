<?php

namespace Cone\Bazar\Traits;

use Illuminate\Support\Facades\Config;

trait InteractsWithStock
{
    /**
     * Get the formatted dimensions.
     */
    public function getFormattedDimensions(string $glue = 'x'): ?string
    {
        $dimensions = $this->metas->whereIn('key', ['length', 'width', 'height'])->filter()->values();

        if ($dimensions->isEmpty()) {
            return null;
        }

        return sprintf('%s %s', $dimensions->implode('value', $glue), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
     */
    public function getFormattedWeight(): ?string
    {
        $weight = $this->metas->firstWhere('key', 'weight');

        if (is_null($weight) || empty($weight->value)) {
            return null;
        }

        return sprintf('%s %s', $weight->value, Config::get('bazar.weight_unit'));
    }

    /**
     * Determine if the stockable model is virtual.
     */
    public function isVirtual(): bool
    {
        $meta = $this->metas->firstWhere('key', 'virtual');

        return ! is_null($meta) && (bool) $meta->value;
    }

    /**
     * Determine if the stockable model is downloadable.
     */
    public function isDownloadable(): bool
    {
        $meta = $this->metas->firstWhere('key', 'downloadable');

        return ! is_null($meta) && (bool) $meta->value;
    }

    /**
     * Determine if the stockable model tracks quantity.
     */
    public function tracksQuantity(): bool
    {
        $meta = $this->metas->firstWhere('key', 'quantity');

        return ! is_null($meta) && ! empty($meta->value);
    }

    /**
     * Determine if the stockable model is available.
     */
    public function isAvailable(float $quantity = 1): bool
    {
        if (! $this->tracksQuantity()) {
            return true;
        }

        $stock = $this->metas->firstWhere('key', 'quantity')?->value ?: 0;

        return min($stock, $quantity) > 0 && $stock >= $quantity;
    }

    /**
     * Increment the quantity by the given value.
     */
    public function incrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $meta = $this->metas->firstWhere('key', 'quantity')
                ?: $this->metas()->make(['key' => 'quantity', 'value' => 0]);

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
            $meta = $this->metas->firstWhere('key', 'quantity')
                ?: $this->metas()->make(['key' => 'quantity', 'value' => 0]);

            $meta->value = max(((float) $meta->value) - $quantity, 0);

            $meta->save();
        }
    }
}
