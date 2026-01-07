<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

interface Inventoryable
{
    /**
     * Get the formatted dimensions.
     */
    public function getFormattedDimensions(): ?string;

    /**
     * Get the formatted weight.
     */
    public function getFormattedWeight(): ?string;

    /**
     * Determine if the model is virtual.
     */
    public function isVirtual(): bool;

    /**
     * Determine if the model is physical.
     */
    public function isPhysical(): bool;

    /**
     * Determine if the model is downloadable.
     */
    public function isDownloadable(): bool;

    /**
     * Determine if the model tracks quantity.
     */
    public function tracksQuantity(): bool;

    /**
     * Determine if the model is available.
     */
    public function isAvailable(float $quantity = 1): bool;

    /**
     * Increment the quantity by the given value.
     */
    public function incrementQuantity(float $quantity = 1): void;

    /**
     * Decrement the quantity by the given value.
     */
    public function decrementQuantity(float $quantity = 1): void;
}
