<?php

namespace Cone\Bazar\Support\Facades;

use Cone\Bazar\Interfaces\Repositories\TaxRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, int|callable $tax)
 * @method static void remove(string $name)
 * @method static void disable()
 * @method static void enable()
 * @method static float calculate(\Cone\Bazar\Interfaces\Taxable $model)
 *
 * @see \Cone\Bazar\Interfaces\Repositories\TaxRepository
 */
class Tax extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return TaxRepository::class;
    }
}
