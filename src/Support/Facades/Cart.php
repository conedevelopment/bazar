<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Cart\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Bazar\Models\Cart model()
 * @method static \Bazar\Models\Item|null item(\Bazar\Models\Product $product, array $properties)
 * @method static void add(\Bazar\Models\Product $product, float $quantity, array $properties)
 * @method static void remove(\Bazar\Models\Item|int|array $item)
 * @method static void update(array $items)
 * @method static void empty()
 * @method static \Illuminate\Support\Collection products()
 * @method static \Illuminate\Support\Collection items()
 * @method static \Bazar\Models\Shipping shipping()
 * @method static float count()
 * @method static bool isEmpty()
 * @method static bool isNotEmpty()
 * @method static \Bazar\Services\Checkout checkout()
 *
 * @see \Bazar\Cart\Driver
 */
class Cart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
