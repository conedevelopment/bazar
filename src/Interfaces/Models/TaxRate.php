<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Taxable;

interface TaxRate
{
    /**
     * Calculate the tax for the taxable model.
     */
    public function calculate(Taxable $taxable): float;
}
