<?php

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
     * Get the itemable model for the item.
     */
    public function itemable(): MorphTo;
}
