<?php

namespace Bazar\Contracts;

interface Discount
{
    /**
     * Calculate the discount for the given model.
     *
     * @param  \Bazar\Contracts\Discountable  $model
     * @return float
     */
    public function calculate(Discountable $model): float;
}
