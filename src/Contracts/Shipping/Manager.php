<?php

namespace Bazar\Contracts\Shipping;

use Bazar\Contracts\Itemable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     *
     * @param  \Bazar\Contracts\Itemable  $model
     * @return void
     */
    public function getAvailableDriversFor(Itemable $model): array;
}
