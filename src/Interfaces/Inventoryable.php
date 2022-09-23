<?php

namespace Cone\Bazar\Interfaces;

interface Inventoryable
{
    /**
     * Get the formatted dimensions.
     *
     * @param  string  $glue
     * @return string|null
     */
    public function getFormattedDimensions(string $glue = 'x'): ?string;

    /**
     * Get the formatted weight.
     *
     * @return string|null
     */
    public function getFormattedWeight(): ?string;

    /**
     * Determine if the stockable model is virtual.
     *
     * @return bool
     */
    public function isVirtual(): bool;

    /**
     * Determine if the stockable model is downloadable.
     *
     * @return bool
     */
    public function isDownloadable(): bool;

    /**
     * Determine if the stockable model tracks quantity.
     *
     * @return bool
     */
    public function tracksQuantity(): bool;

    /**
     * Determine if the stockable model is available.
     *
     * @param  float  $quantity
     * @return bool
     */
    public function isAvailable(float $quantity = 1): bool;

    /**
     * Increment the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function incrementQuantity(float $quantity = 1): void;

    /**
     * Decrement the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function decrementQuantity(float $quantity = 1): void;
}
