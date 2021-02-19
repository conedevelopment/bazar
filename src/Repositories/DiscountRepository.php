<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Discount;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Repositories\DiscountRepository as Contract;
use Closure;

class DiscountRepository extends Repository implements Contract
{
    /**
     * Determine if the discounts are disabled.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * Register a new discount.
     *
     * @param  string  $name
     * @param  int|callable  $discount
     * @return void
     */
    public function register(string $name, $discount): void
    {
        $this->items->put($name, $discount);
    }

    /**
     * Disable the discount calculation.
     *
     * @return void
     */
    public function disable(): void
    {
        $this->disabled = true;
    }

    /**
     * Enable the discount calculation.
     *
     * @return void
     */
    public function enable(): void
    {
        $this->disabled = false;
    }

    /**
     * Calculate the total of the processed discounts.
     *
     * @param  \Bazar\Contracts\Discountable  $model
     * @return float
     */
    public function calculate(Discountable $model): float
    {
        return $this->disabled
            ? $model->discount
            : $this->items->sum(function ($discount) use ($model): float {
                return $this->process($model, $discount);
            });
    }

    /**
     * Process the calculation.
     *
     * @param  \Bazar\Contracts\Discountable  $model
     * @param  string|float|\Closure|\Bazar\Contracts\Discount  $discount
     * @return float
     */
    protected function process(Discountable $model, $discount): float
    {
        if (is_numeric($discount)) {
            return $discount;
        }

        if ($discount instanceof Closure) {
            return call_user_func_array($discount, [$model]);
        }

        if (is_callable([$discount, 'calculate'], true) && in_array(Discount::class, class_implements($discount))) {
            return call_user_func_array(
                [is_string($discount) ? new $discount : $discount, 'calculate'], [$model]
            );
        }

        return 0;
    }
}
