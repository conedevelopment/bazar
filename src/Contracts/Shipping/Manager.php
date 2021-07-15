<?php

namespace Cone\Bazar\Contracts\Shipping;

use Cone\Bazar\Contracts\Itemable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     *
     * @param  \Cone\Bazar\Contracts\Itemable|null  $model
     * @return array
     */
    public function getAvailableDrivers(?Itemable $model = null): array;
}
