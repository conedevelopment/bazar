<?php

namespace Cone\Bazar\Repositories;

use Closure;
use Cone\Bazar\Interfaces\Discount;
use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Interfaces\Repositories\DiscountRepository as Contract;

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
     * @param  int|callable  $discount
     */
    public function register(string $name, $discount): void
    {
        $this->items->put($name, $discount);
    }

    /**
     * Disable the discount calculation.
     */
    public function disable(): void
    {
        $this->disabled = true;
    }

    /**
     * Enable the discount calculation.
     */
    public function enable(): void
    {
        $this->disabled = false;
    }

    /**
     * Calculate the total of the processed discounts.
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
     * @param  string|float|\Closure|\Cone\Bazar\Interfaces\Discount  $discount
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
