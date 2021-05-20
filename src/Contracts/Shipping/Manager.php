<?php

namespace Bazar\Contracts\Shipping;

use Bazar\Contracts\Itemable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     *
     * @param  \Bazar\Contracts\Itemable|null  $model
     * @return array
     */
    public function getAvailableDrivers(?Itemable $model = null): array;
}
