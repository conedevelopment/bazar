<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Repositories\TaxRepository as Contract;
use Bazar\Contracts\Tax;
use Bazar\Contracts\Taxable;
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
     * Remove the given tax.
     *
     * @param  string  $name
     * @return void
     */
    public function remove(string $name): void
    {
        $this->items->forget($name);
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
     * @param  \Bazar\Contracts\Taxable  $model
     * @return float
     */
    public function calculate(Taxable $model): float
    {
        return ! $this->disabled ? $this->items->sum(function ($tax) use ($model): float {
            return $this->process($model, $tax);
        }) : $model->tax;
    }

    /**
     * Process the calculation.
     *
     * @param  \Bazar\Contracts\Taxable  $model
     * @param  string|float|\Closure|\Bazar\Contracts\Tax  $tax
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

        if ((is_string($tax) || is_object($tax)) && in_array(Tax::class, class_implements($tax))) {
            return call_user_func_array(
                [is_string($tax) ? new $tax : $tax, 'calculate'], [$model]
            );
        }

        return 0;
    }
}
