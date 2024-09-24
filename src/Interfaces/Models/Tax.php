<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Tax
{
    /**
     * Get the taxable model for the model.
     */
    public function taxable(): MorphTo;

    /**
     * Get the tax rate for the model.
     */
    public function taxRate(): BelongsTo;

    /**
     * Get the formatted tax.
     */
    public function format(): string;
}
