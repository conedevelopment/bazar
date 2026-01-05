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
    public const VERSION = '1.5.1';

    /**
     * The currency in use.
     */
    protected static ?Currency $currency = null;

    /**
     * Get all the available currencies.
     */
    public static function getCurrencies(): array
    {
        $currencies = array_filter(Currency::cases(), static function (Currency $currency): bool {
            return $currency->available();
        });

        return array_values($currencies);
    }

    /**
     * Get the default currency.
     */
    public static function getDefaultCurrency(): Currency
    {
        return Currency::tryFrom(Config::get('bazar.currencies.default', 'USD')) ?: Currency::USD;
    }

    /**
     * Get the currency in use.
     */
    public static function getCurrency(): Currency
    {
        return static::$currency ??= static::getDefaultCurrency();
    }

    /**
     * Set the currency in use.
     */
    public static function setCurrency(Currency $currency): void
    {
        static::$currency = $currency;
    }
}
