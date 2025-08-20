<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Repositories;

use Closure;
use Cone\Bazar\Interfaces\Tax;
use Cone\Bazar\Interfaces\Taxable;

interface TaxRepository
{
    /**
     * Register a new tax.
     */
    public function register(string $name, int|float|Closure|Tax $tax): void;

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
