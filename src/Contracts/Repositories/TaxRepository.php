<?php

namespace Bazar\Contracts\Repositories;

use Bazar\Contracts\Taxable;

interface TaxRepository
{
    /**
     * Register a new tax.
     *
     * @param  mixed  $name
     * @param  int|callable  $tax
     * @return void
     */
    public function register(string $name, $tax): void;

    /**
     * Remove the given tax.
     *
     * @param  string  $name
     * @return void
     */
    public function remove(string $name): void;

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
     * @param  \Bazar\Contracts\Taxable  $model
     * @return float
     */
    public function calculate(Taxable $model): float;
}
