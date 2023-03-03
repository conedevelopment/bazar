<?php

namespace Cone\Bazar\Repositories;

use Closure;
use Cone\Bazar\Interfaces\Repositories\TaxRepository as Contract;
use Cone\Bazar\Interfaces\Tax;
use Cone\Bazar\Interfaces\Taxable;

class TaxRepository extends Repository implements Contract
{
    /**
     * Determine if the taxes are disabled.
     */
    protected bool $disabled = false;

    /**
     * Register a new tax.
     */
    public function register(string $name, int|float|Closure|Tax $tax): void
    {
        $this->items->put($name, $tax);
    }

    /**
     * Disable the tax calculation.
     */
    public function disable(): void
    {
        $this->disabled = true;
    }

    /**
     * Enable the tax calculation.
     */
    public function enable(): void
    {
        $this->disabled = false;
    }

    /**
     * Calculate tax for the given item.
     */
    public function calculate(Taxable $model): float
    {
        return $this->disabled
            ? $model->tax
            : $this->items->sum(function ($tax) use ($model): float {
                return $this->process($model, $tax);
            });
    }

    /**
     * Process the calculation.
     */
    protected function process(Taxable $model, int|float|Closure|Tax $tax): float
    {
        if (is_numeric($tax)) {
            return $tax;
        }

        if ($tax instanceof Closure) {
            return call_user_func_array($tax, [$model]);
        }

        if ($tax instanceof Tax) {
            return call_user_func_array([$tax, '__invoke'], [$model]);
        }

        return 0;
    }
}
