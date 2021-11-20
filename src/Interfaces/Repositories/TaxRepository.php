<?php

namespace Cone\Bazar\Interfaces\Repositories;

use Cone\Bazar\Interfaces\Taxable;

interface TaxRepository
{
    /**
     * Register a new tax.
     *
     * @param  string  $name
     * @param  int|callable  $tax
     * @return void
     */
    public function register(string $name, $tax): void;

    /**
     * Disable the tax calculation.
     *
     * @return void
     */
    public function disable(): void;

    /**
     * Enable the tax calculation.
     *
     * @return void
     */
    public function enable(): void;

    /**
     * Calculate tax for the given model.
     *
     * @param  \Cone\Bazar\Interfaces\Taxable  $model
     * @return float
     */
    public function calculate(Taxable $model): float;
}
