<?php

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;

interface Buyable
{
    /**
     * Get the item representation of the buyable instance.
     */
    public function toItem(Itemable $itemable, array $attributes = []): Item;
}
