<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Taxable
{
    /**
     * Get the taxes for the model.
     */
    public function taxes(): MorphToMany;

    /**
     * Get the tax base.
     */
    public function getTaxBase(): float;

    /**
     * Get the tax total.
     */
    public function getTax(): float;

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string;

    /**
     * Get the tax total total.
     */
    public function getTaxTotal(): float;

    /**
     * Get the formatted tax total.
     */
    public function getFormattedTaxTotal(): string;

    /**
     * Calculate the taxes.
     */
    public function calculateTaxes(): float;
}
