<?php

namespace Bazar\Contracts;

use Bazar\Models\Item;

interface Buyable
{
    /**
     * Get the item representation of the buyable instance.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  float|null  $quantity
     * @param  array  $properties
     * @return \Bazar\Models\Item
     */
    public function toItem(Itemable $itemable, ?float $quantity = null, array $properties = []): Item;
}
