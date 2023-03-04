<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface PropertyValue
{
    /**
     * Get the property for the property value.
     */
    public function property(): BelongsTo;
}
