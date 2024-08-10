<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Tax
{
    /**
     * Get the tax rate for the model.
     */
    public function rate(): BelongsTo;

    /**
     * Get the taxable model.
     */
    public function taxable(): MorphTo;
}
