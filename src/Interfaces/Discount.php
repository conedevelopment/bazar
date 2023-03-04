<?php

namespace Cone\Bazar\Interfaces;

interface Discount
{
    /**
     * Calculate the discount for the given model.
     */
    public function __invoke(Discountable $model): float;
}
