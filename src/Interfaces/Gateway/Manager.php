<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Gateway;

use Cone\Bazar\Interfaces\Checkoutable;

interface Manager
{
    /**
     * Get the available drivers for the given model.
     */
    public function getAvailableDrivers(?Checkoutable $model = null): array;
}
