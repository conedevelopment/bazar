<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\TaxRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, int|callable $tax)
 * @method static void remove(string $name)
 * @method static void disable()
 * @method static void enable()
 * @method static float calculate(\Bazar\Contracts\Taxable $model)
 *
 * @see \Bazar\Contracts\Repositories\TaxRepository
 */
class Tax extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return TaxRepository::class;
    }
}
