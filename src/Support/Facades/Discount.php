<?php

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Contracts\Repositories\DiscountRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, int|callable $discount)
 * @method static void remove(string $name)
 * @method static void disable()
 * @method static void enable()
 * @method static float calculate(\Cone\Bazar\Contracts\Discountable $model)
 *
 * @see \Cone\Bazar\Contracts\Repositories\DiscountRepository
 */
class Discount extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return DiscountRepository::class;
    }
}
