<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Interfaces\Checkoutable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Cart extends Discountable, Checkoutable
{
    /**
     * Get the order for the cart.
     */
    public function order(): BelongsTo;

    /**
     * Lock the cart.
     */
    public function lock(): void;

    /**
     * Unlock the cart.
     */
    public function unlock(): void;
}
