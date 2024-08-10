<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Taxable;
use Cone\Bazar\Models\Tax;

interface TaxRate
{
    /**
     * Determine wheter the discount rate is applicable on the model.
     */
    public function applicable(Taxable $model): bool;

    /**
     * Calculate the discount for the given model.
     */
    public function calculate(Taxable $model): float;

    /**
     * Apply the discount rate on the model.
     */
    public function apply(Taxable $model): ?Tax;
}
