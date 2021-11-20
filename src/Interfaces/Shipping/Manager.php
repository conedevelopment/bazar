<?php

namespace Cone\Bazar\Interfaces\Shipping;

use Cone\Bazar\Interfaces\Itemable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     *
     * @param  \Cone\Bazar\Interfaces\Itemable|null  $model
     * @return array
     */
    public function getAvailableDrivers(?Itemable $model = null): array;
}
