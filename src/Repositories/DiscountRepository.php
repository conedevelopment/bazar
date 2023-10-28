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
     */
    protected bool $disabled = false;

    /**
     * Register a new discount.
     */
    public function register(string $name, int|float|Closure|Discount $discount): void
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
            : $this->items->sum(function (int|float|Closure|Discount $discount) use ($model): float {
                return $this->process($model, $discount);
            });
    }

    /**
     * Process the calculation.
     */
    protected function process(Discountable $model, int|float|Closure|Discount $discount): float
    {
        if (is_numeric($discount)) {
            return $discount;
        }

        if ($discount instanceof Closure) {
            return call_user_func_array($discount, [$model]);
        }

        if ($discount instanceof Discount) {
            return call_user_func_array([$discount, '__invoke'], [$model]);
        }

        return 0;
    }
}
