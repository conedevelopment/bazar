<?php

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
     * Get the total.
     */
    public function getTotal(): float;

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string;

    /**
     * Get the net total.
     */
    public function getNetTotal(): float;

    /**
     * Get the formatted net total.
     */
    public function getFormattedNetTotal(): string;

    /**
     * Get the quantity.
     */
    public function getQuantity(): float;
}
