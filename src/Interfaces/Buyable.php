<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;

interface Buyable
{
    /**
     * Determine whether the buyable object is available for the itemable instance.
     */
    public function buyable(Itemable $itemable): bool;

    /**
     * Get the item representation of the buyable instance.
     */
    public function toItem(Itemable $itemable, array $attributes = []): Item;
}
