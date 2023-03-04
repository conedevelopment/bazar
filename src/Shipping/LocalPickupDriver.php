<?php

namespace Cone\Bazar\Shipping;

use Cone\Bazar\Interfaces\Shippable;

class LocalPickupDriver extends Driver
{
    /**
     * Calculate the shipping cost.
     */
    public function calculate(Shippable $model): float
    {
        return 0;
    }
}
