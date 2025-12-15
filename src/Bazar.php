<?php

declare(strict_types=1);

namespace Cone\Bazar;

use Cone\Bazar\Enums\Currency;
use Illuminate\Support\Facades\Config;

abstract class Bazar
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '1.3.0';

    /**
     * The currency in use.
     */
    protected static ?Currency $currency = null;

    /**
     * Get all the available currencies.
     */
    public static function getCurrencies(): array
    {
        return Config::get('bazar.currencies.available', []);
    }

    /**
     * Get the currency in use.
     */
    public static function getCurrency(): Currency
    {
        return static::$currency ??= Currency::from(Config::get('bazar.currencies.default', 'USD'));
    }

    /**
     * Set the currency in use.
     */
    public static function setCurrency(Currency $currency): void
    {
        static::$currency = $currency;
    }
}
