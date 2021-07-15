<?php

namespace Cone\Bazar\Contracts;

interface LineItem extends Taxable
{
    /**
     * Get the price.
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Get the formatted price.
     *
     * @return string
     */
    public function getFormattedPrice(): string;

    /**
     * Get the total.
     *
     * @return float
     */
    public function getTotal(): float;

    /**
     * Get the formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string;

    /**
     * Get the net total.
     *
     * @return float
     */
    public function getNetTotal(): float;

    /**
     * Get the formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string;

    /**
     * Get the quantity.
     *
     * @return float
     */
    public function getQuantity(): float;
}
