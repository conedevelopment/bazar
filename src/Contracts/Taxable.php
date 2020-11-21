<?php

namespace Bazar\Contracts;

interface Taxable
{
    /**
     * Get the formatted tax attribute.
     *
     * @return string
     */
    public function getFormattedTaxAttribute(): string;

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function formattedTax(): string;

    /**
     * Calculate the tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function tax(bool $update = true): float;
}
