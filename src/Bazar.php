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
    public const VERSION = '1.0.0-alpha';

    /**
     * The currency in use.
     */
    protected static ?string $currency = null;

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
    public static function getCurrency(): string
    {
        return static::$currency ?: Config::get('bazar.currencies.default', 'usd');
    }

    /**
     * Set the currency in use.
     *
     *
     * @throws \Cone\Bazar\Exceptions\InvalidCurrencyException
     */
    public static function setCurrency(string $currency): void
    {
        $currency = strtolower($currency);

        if (! array_key_exists($currency, static::getCurrencies())) {
            throw new InvalidCurrencyException("The [{$currency}] currency is not registered.");
        }

        static::$currency = $currency;
    }
}
