<?php

namespace Bazar\Shipping;

use Bazar\Contracts\Shippable;

class LocalPickupDriver extends Driver
{
    /**
     * Calculate the shipping cost.
     *
     * @param  \Bazar\Contracts\Shippable  $model
     * @return float
     */
    public function calculate(Shippable $model): float
    {
        return 0.0;
    }
}
