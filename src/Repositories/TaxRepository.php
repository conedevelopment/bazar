<?php

namespace Cone\Bazar\Repositories;

use Cone\Bazar\Interfaces\Repositories\TaxRepository as Contract;
use Cone\Bazar\Interfaces\Tax;
use Cone\Bazar\Interfaces\Taxable;
use Closure;

class TaxRepository extends Repository implements Contract
{
    /**
     * Determine if the taxes are disabled.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * Register a new tax.
     *
     * @param  string  $name
     * @param  int|callable  $tax
     * @return void
     */
    public function register(string $name, $tax): void
    {
        $this->items->put($name, $tax);
    }

    /**
     * Disable the tax calculation.
     *
     * @return void
     */
    public function disable(): void
    {
        $this->disabled = true;
    }

    /**
     * Enable the tax calculation.
     *
     * @return void
     */
    public function enable(): void
    {
        $this->disabled = false;
    }

    /**
     * Calculate tax for the given item.
     *
     * @param  \Cone\Bazar\Interfaces\Taxable  $model
     * @return float
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
     *
     * @param  \Cone\Bazar\Interfaces\Taxable  $model
     * @param  string|float|\Closure|\Cone\Bazar\Interfaces\Tax  $tax
     * @return float
     */
    protected function process(Taxable $model, $tax): float
    {
        if (is_numeric($tax)) {
            return $tax;
        }

        if ($tax instanceof Closure) {
            return call_user_func_array($tax, [$model]);
        }

        if (is_callable([$tax, 'calculate'], true) && in_array(Tax::class, class_implements($tax))) {
            return call_user_func_array(
                [is_string($tax) ? new $tax : $tax, 'calculate'], [$model]
            );
        }

        return 0;
    }
}
