<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Discount
{
    /**
     * Get the discount rate for the model.
     */
    public function rate(): BelongsTo;

    /**
     * Get the discountable model.
     */
    public function discountable(): MorphTo;
}
