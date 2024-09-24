<?php

namespace Cone\Bazar\Shipping;

use Cone\Bazar\Interfaces\Shippable;
use Cone\Bazar\Support\Driver as BaseDriver;

abstract class Driver extends BaseDriver
{
    /**
     * Calculate the shipping fee.
     */
    abstract public function calculate(Shippable $model): float;
}
