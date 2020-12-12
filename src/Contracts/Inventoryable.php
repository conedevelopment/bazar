<?php

namespace Bazar\Contracts;

use Bazar\Support\Bags\Inventory;

interface Inventoryable
{
    /**
     * Get the inventory attribute.
     *
     * @param  string  $value
     * @return \Bazar\Support\Bags\Inventory
     */
    public function getInventoryAttribute(string $value): Inventory;

    /**
     * Set the inventory attribute.
     *
     * @param  array  $value
     * @return void
     */
    public function setInventoryAttribute(array $value): void;
}
