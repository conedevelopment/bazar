<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Discountable;
use Bazar\Contracts\Itemable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Cart extends Discountable, Itemable
{
    /**
     * Get the order for the cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo;
    /**
     * Lock the cart.
     *
     * @return void
     */
    public function lock(): void;

    /**
     * Unlock the cart.
     *
     * @return void
     */
    public function unlock(): void;
}
