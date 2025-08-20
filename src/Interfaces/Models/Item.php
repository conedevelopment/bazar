<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\LineItem;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Item extends LineItem
{
    /**
     * Get the buyable model for the item.
     */
    public function buyable(): MorphTo;

    /**
     * Get the checkoutable model for the item.
     */
    public function checkoutable(): MorphTo;

    /**
     * Determine if the item is a line item.
     */
    public function isLineItem(): bool;

    /**
     * Determine if the item is a fee.
     */
    public function isFee(): bool;
}
