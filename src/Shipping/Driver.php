<?php

namespace Cone\Bazar\Shipping;

use Cone\Bazar\Contracts\Shippable;
use Cone\Bazar\Support\Driver as BaseDriver;

abstract class Driver extends BaseDriver
{
    /**
     * Calculate the shipping cost.
     *
     * @param  \Cone\Bazar\Contracts\Shippable  $model
     * @return float
     */
    abstract public function calculate(Shippable $model): float;
}
