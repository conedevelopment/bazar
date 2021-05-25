<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Cart\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Bazar\Models\Cart getModel()
 * @method static \Bazar\Models\Item|null getItem(string $id)
 * @method static \Bazar\Models\Item addItem(\Bazar\Contracts\Buyable $buyable, float $quantity, array $properties)
 * @method static void removeItem(string $id)
 * @method static void removeItems(array $ids)
 * @method static void updateItem(string $id, array $properties)
 * @method static void updateItems(array $data)
 * @method static \Illuminate\Support\Collection getItems()
 * @method static \Bazar\Models\Shipping getBilling()
 * @method static void updateBilling(array $attributes)
 * @method static \Bazar\Models\Shipping getShipping()
 * @method static void updateShipping(array $attributes, string $driver)
 * @method static void empty()
 * @method static float count()
 * @method static bool isEmpty()
 * @method static bool isNotEmpty()
 * @method static \Bazar\Models\Order checkout(string $driver)
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
