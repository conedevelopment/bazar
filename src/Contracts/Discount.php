<?php

namespace Cone\Bazar\Contracts;

interface Discount
{
    /**
     * Calculate the discount for the given model.
     *
     * @param  \Cone\Bazar\Contracts\Discountable  $model
     * @return float
     */
    public function calculate(Discountable $model): float;
}
