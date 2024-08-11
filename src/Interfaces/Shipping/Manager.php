<?php

namespace Cone\Bazar\Interfaces\Shipping;

use Cone\Bazar\Interfaces\Checkoutable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     */
    public function getAvailableDrivers(?Checkoutable $model = null): array;
}
