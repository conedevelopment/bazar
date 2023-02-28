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
        $dimensions = array_filter($this->getAttributes(['metas.length', 'metas.width', 'metas.height']));

        if (empty($dimensions)) {
            return null;
        }

        return sprintf('%s %s', implode($glue, $dimensions), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
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
     */
    public function isVirtual(): bool
    {
        return (bool) $this->getAttribute('metas.virtual');
    }

    /**
     * Determine if the stockable model is downloadable.
     */
    public function isDownloadable(): bool
    {
        return (bool) $this->getAttribute('metas.downloadable');
    }

    /**
     * Determine if the stockable model tracks quantity.
     */
    public function tracksQuantity(): bool
    {
        return ! is_null($this->getAttribute('metas.quantity'));
    }

    /**
     * Determine if the stockable model is available.
     */
    public function isAvailable(float $quantity = 1): bool
    {
        $stock = $this->getAttribute('metas.quantity') ?: 0;

        return ! $this->tracksQuantity() || (min($stock, $quantity) > 0 && $stock >= $quantity);
    }

    /**
     * Increment the quantity by the given value.
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
     */
    public function decrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $value = $this->getAttribute('metas.quantity') ?: 0;

            $this->setAttribute('metas.quantity', max($value - $quantity, 0));
        }
    }
}
