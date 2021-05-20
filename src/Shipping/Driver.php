<?php

namespace Bazar\Shipping;

use Bazar\Contracts\Shippable;
use Bazar\Support\Driver as BaseDriver;

abstract class Driver extends BaseDriver
{
    /**
     * Calculate the shipping cost.
     *
     * @param  \Bazar\Contracts\Shippable  $model
     * @return float
     */
    abstract public function calculate(Shippable $model): float;
}
