<?php

namespace Bazar\Contracts\Gateway;

use Bazar\Contracts\Itemable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     *
     * @param  \Bazar\Contracts\Itemable|null  $model
     * @return void
     */
    public function getAvailableDrivers(?Itemable $model = null): array;
}
