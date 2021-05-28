<?php

namespace Bazar\Contracts;

use Bazar\Models\Item;

interface Buyable
{
    /**
     * Get the item representation of the buyable instance.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $attributes
     * @return \Bazar\Models\Item
     */
    public function toItem(Itemable $itemable, array $attributes = []): Item;
}
