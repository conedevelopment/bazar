<?php

namespace Cone\Bazar\Interfaces\Repositories;

use Cone\Bazar\Interfaces\Taxable;

interface TaxRepository
{
    /**
     * Register a new tax.
     *
     * @param  int|callable  $tax
     */
    public function register(string $name, $tax): void;

    /**
     * Disable the tax calculation.
     */
    public function disable(): void;

    /**
     * Enable the tax calculation.
     */
    public function enable(): void;

    /**
     * Calculate tax for the given model.
     */
    public function calculate(Taxable $model): float;
}
