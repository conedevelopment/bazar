<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\LineItem;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Item extends LineItem
{
    /**
     * Get the buyable model for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function buyable(): MorphTo;

    /**
     * Get the itemable model for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function itemable(): MorphTo;
}
