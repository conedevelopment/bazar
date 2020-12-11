<?php

namespace Bazar\Support\Bags;

use Illuminate\Support\Str;

class Price extends Bag
{
    /**
     * The currency.
     *
     * @var string
     */
    protected $currency;

    /**
     * The bag items.
     *
     * @var array
     */
    protected $items = [
        'default' => null,
        'sale' => null,
    ];

    /**
     * Create a new bag instance.
     *
     * @param  string  $currency
     * @param  array  $items
     * @return void
     */
    public function __construct(string $currency, array $items = [])
    {
        $this->currency = $currency;

        parent::__construct($items);
    }

    /**
     * Get the currency.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get the formatted price of the given type.
     *
     * @param  string  $type
     * @return string|null
     */
    public function format(string $type = 'default'): ?string
    {
        $price = $this->get($type);

        return $price ? Str::currency($price, $this->currency) : null;
    }
}
