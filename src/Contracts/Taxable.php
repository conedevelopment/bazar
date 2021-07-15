<?php

namespace Cone\Bazar\Contracts;

interface Taxable
{
    /**
     * Get the tax.
     *
     * @return float
     */
    public function getTax(): float;

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function getFormattedTax(): string;

    /**
     * Calculate the tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateTax(bool $update = true): float;
}
