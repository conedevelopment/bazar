<?php

namespace Cone\Bazar\Interfaces;

interface Taxable
{
    /**
     * Get the tax.
     */
    public function getTax(): float;

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string;

    /**
     * Get the tax rate.
     */
    public function getTaxRate(): float;

    /**
     * Get the formatted tax rate.
     */
    public function getFormattedTaxRate(): string;

    /**
     * Calculate the tax.
     */
    public function calculateTax(bool $update = true): float;
}
