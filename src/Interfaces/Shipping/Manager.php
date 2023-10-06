<?php

namespace Cone\Bazar\Interfaces\Shipping;

use Cone\Bazar\Interfaces\Itemable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     */
    public function getAvailableDrivers(Itemable $model = null): array;
}
