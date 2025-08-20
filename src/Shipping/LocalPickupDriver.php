<?php

declare(strict_types=1);

namespace Cone\Bazar\Shipping;

use Cone\Bazar\Interfaces\Shippable;

class LocalPickupDriver extends Driver
{
    /**
     * Calculate the shipping fee.
     */
    public function calculate(Shippable $model): float
    {
        return 0;
    }
}
