<?php

namespace Cone\Bazar\Shipping;

use Cone\Bazar\Interfaces\Shippable;

class LocalPickupDriver extends Driver
{
    /**
     * Calculate the shipping cost.
     *
     * @param  \Cone\Bazar\Interfaces\Shippable  $model
     * @return float
     */
    public function calculate(Shippable $model): float
    {
        return 0;
    }
}
