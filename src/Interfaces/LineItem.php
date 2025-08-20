<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

interface LineItem extends Taxable
{
    /**
     * Get the price.
     */
    public function getName(): string;

    /**
     * Get the price.
     */
    public function getPrice(): float;

    /**
     * Get the formatted price.
     */
    public function getFormattedPrice(): string;

    /**
     * Get the gross price.
     */
    public function getGrossPrice(): float;

    /**
     * Get the formatted price.
     */
    public function getFormattedGrossPrice(): string;

    /**
     * Get the total.
     */
    public function getTotal(): float;

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string;

    /**
     * Get the subtotal.
     */
    public function getSubtotal(): float;

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotal(): string;

    /**
     * Get the quantity.
     */
    public function getQuantity(): float;
}
