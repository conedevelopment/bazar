<?php

declare(strict_types=1);

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Interfaces\Repositories\DiscountRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, int|callable $discount)
 * @method static void remove(string $name)
 * @method static void disable()
 * @method static void enable()
 * @method static float calculate(\Cone\Bazar\Interfaces\Discountable $model)
 *
 * @see \Cone\Bazar\Interfaces\Repositories\DiscountRepository
 */
class Discount extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return DiscountRepository::class;
    }
}
