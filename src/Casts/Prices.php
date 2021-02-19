<?php

namespace Bazar\Casts;

use Bazar\Bazar;
use Bazar\Support\AttributeBag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Prices extends AttributeBag
{
    /**
     * Create a new bag instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $items = array_replace_recursive(
            array_fill_keys(array_keys(Bazar::currencies()), ['default' => null]),
            $items
        );

        parent::__construct($items);
    }

    /**
     * Get the formatted price of the given type.
     *
     * @param  string|null  $key
     * @return string|null
     */
    public function format(string $key = null): ?string
    {
        $currency = $key ? explode('.', $key, 2)[0] : Bazar::currency();

        $price = Arr::get(
            $this->toArray(), $key ?: "{$currency}.default"
        );

        return $price ? Str::currency($price, $currency) : null;
    }
}
