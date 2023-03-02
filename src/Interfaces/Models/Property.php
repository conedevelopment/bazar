<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface Property
{
    /**
     * Get the values for the property.
     */
    public function values(): HasMany;
}
