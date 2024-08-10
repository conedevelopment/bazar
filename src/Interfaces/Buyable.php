<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;

interface Buyable
{
    /**
     * Determine whether the buyable object is available for the checkoutable instance.
     */
    public function buyable(Checkoutable $checkoutable): bool;

    /**
     * Get the item representation of the buyable instance.
     */
    public function toItem(Checkoutable $checkoutable, array $attributes = []): Item;
}
