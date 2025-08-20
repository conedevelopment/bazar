<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface PropertyValue
{
    /**
     * Get the property for the property value.
     */
    public function property(): BelongsTo;
}
