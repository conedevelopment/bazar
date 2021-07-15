<?php

namespace Cone\Bazar\Contracts;

use Cone\Bazar\Models\Item;

interface Buyable
{
    /**
     * Get the item representation of the buyable instance.
     *
     * @param  \Cone\Bazar\Contracts\Itemable  $itemable
     * @param  array  $attributes
     * @return \Cone\Bazar\Models\Item
     */
    public function toItem(Itemable $itemable, array $attributes = []): Item;
}
