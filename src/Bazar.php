<?php

namespace Cone\Bazar;

use Cone\Bazar\Exceptions\InvalidCurrencyException;
use Illuminate\Support\Facades\Config;

abstract class Bazar
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '1.2.0';

    /**
     * The currency in use.
     */
    protected static ?string $currency = null;

    /**
     * Get all the available currencies.
     */
    public static function getCurrencies(): array
    {
        return array_keys(Config::get('bazar.currencies.available', []));
    }

    /**
     * Get the currency in use.
     */
    public static function getCurrency(): string
    {
        return strtoupper(static::$currency ?: Config::get('bazar.currencies.default', 'USD'));
    }

    /**
     * Set the currency in use.
     */
    public static function setCurrency(string $currency): void
    {
        $currency = strtoupper($currency);

        if (! in_array($currency, static::getCurrencies())) {
            throw new InvalidCurrencyException("The [{$currency}] currency is not registered.");
        }

        static::$currency = $currency;
    }
}
