<?php

namespace Bazar\Casts;

use Bazar\Bazar;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Prices extends AttributeBag
{
    /**
     * Create a new attribute bag instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->defaults = array_fill_keys(
            array_keys(Bazar::getCurrencies()),
            ['default' => null]
        );

        parent::__construct($items);
    }

    /**
     * Get the formatted price of the given type.
     *
     * @param  string|null  $key
     * @return string|null
     */
    public function format(?string $key = null): ?string
    {
        $currency = $key ? explode('.', $key, 2)[0] : Bazar::getCurrency();

        $price = Arr::get(
            $this->toArray(), $key ?: "{$currency}.default"
        );

        return $price ? Str::currency($price, $currency) : null;
    }
}
