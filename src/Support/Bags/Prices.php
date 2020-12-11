<?php

namespace Bazar\Support\Bags;

use Bazar\Bazar;
use InvalidArgumentException;

class Prices extends Bag
{
    /**
     * Create a new bag instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $items = array_replace_recursive(array_fill_keys(
            array_keys(Bazar::currencies()), []
        ), $items);

        foreach ($items as $currency => $values) {
            $this->items[$currency] = new Price($currency, $values);
        }
    }

    /**
     * Get the value of the given offset.
     *
     * @param  string|int|null  $key
     * @param  \Bazar\Support\Bags\Price  $value
     * @return void
     *
     * @throws \InvalidArgimentException
     */
    public function offsetSet($key, $value): void
    {
        if (! $value instanceof Price) {
            throw new InvalidArgumentException('The given value must be a [Bazar\\Support\\Bags\\Price] instance.');
        }

        parent::offsetSet($key, $value);
    }
}
