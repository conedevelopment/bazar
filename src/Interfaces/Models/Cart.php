<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Checkoutable;
use Cone\Bazar\Interfaces\Discountable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Cart extends Checkoutable, Discountable
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

    /**
     * Validate the cart.
     */
    public function validate(): bool;
}
