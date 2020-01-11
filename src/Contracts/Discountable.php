<?php

namespace Bazar\Contracts;

interface Discountable
{
    /**
     * Calculate the discount.
     *
     * @param  bool  $update
     * @return float
     */
    public function discount(bool $update = true): float;
}
