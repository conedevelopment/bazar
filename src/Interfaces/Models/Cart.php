<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Interfaces\Itemable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Cart extends Discountable, Itemable
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
