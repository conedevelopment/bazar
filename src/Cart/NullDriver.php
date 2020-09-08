<?php

namespace Bazar\Cart;

use Bazar\Models\Cart;
use Bazar\Models\Item;
use Bazar\Models\Product;

class NullDriver extends Driver
{
    /**
     * Get the item by the product and its properties.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  array  $properties
     * @return \Bazar\Models\Item|null
     */
    public function item(Product $product, array $properties = []): ?Item
    {
        return new Item;
    }

    /**
     * Add the product with the given properties to the cart.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  float  $quantity
     * @param  array  $properties
     * @return void
     */
    public function add(Product $product, float $quantity = 1, array $properties = []): void
    {
        //
    }

    /**
     * Remove the given item from the cart.
     *
     * @param  \Bazar\Models\Item|int  $item
     * @return void
     */
    public function remove($item): void
    {
        //
    }

    /**
     * Update the cart items and shipping.
     *
     * @param  array  $items
     * @return void
     */
    public function update(array $items = []): void
    {
        //
    }

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function empty(): void
    {
        //
    }

    /**
     * Resolve the cart instance.
     *
     * @return \Bazar\Models\Cart
     */
    protected function resolve(): Cart
    {
        return new Cart;
    }
}
