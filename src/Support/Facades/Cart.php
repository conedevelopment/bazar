<?php

declare(strict_types=1);

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Interfaces\Cart\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Cone\Bazar\Models\Cart getModel()
 * @method static \Cone\Bazar\Models\Item|null getItem(string $id)
 * @method static \Cone\Bazar\Models\Item addItem(\Cone\Bazar\Interfaces\Buyable $buyable, float $quantity, array $properties)
 * @method static void removeItem(string $id)
 * @method static void removeItems(array $ids)
 * @method static void updateItem(string $id, array $properties)
 * @method static void updateItems(array $data)
 * @method static \Illuminate\Support\Collection getItems()
 * @method static \Cone\Bazar\Models\Shipping getBilling()
 * @method static void updateBilling(array $attributes)
 * @method static \Cone\Bazar\Models\Shipping getShipping()
 * @method static void updateShipping(array $attributes, string $driver)
 * @method static void empty()
 * @method static float count()
 * @method static bool isEmpty()
 * @method static bool isNotEmpty()
 * @method static bool validate()
 * @method static void sync()
 * @method static bool applyCoupon(string|\Cone\Bazar\Models\Coupon $coupon)
 * @method static void removeCoupon(string|\Cone\Bazar\Models\Coupon $coupon)
 * @method static \Cone\Bazar\Gateway\Response checkout(string $driver)
 *
 * @see \Cone\Bazar\Cart\Driver
 */
class Cart extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
