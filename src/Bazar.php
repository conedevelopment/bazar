<?php

namespace Bazar;

use Bazar\Exceptions\InvalidCurrencyException;
use Bazar\Http\Middleware\HandleInertiaRequests;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

abstract class Bazar
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '0.8.2';

    /**
     * The default currency.
     *
     * @var string|null
     */
    protected static ?string $currency = null;

    /**
     * Get the version.
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Get all the currenciess.
     *
     * @return array
     */
    public static function getCurrencies(): array
    {
        return array_flip(Config::get('bazar.currencies.available', []));
    }

    /**
     * Get the currency in use.
     *
     * @return string
     */
    public static function getCurrency(): string
    {
        return static::$currency ?: Config::get('bazar.currencies.default', 'usd');
    }

    /**
     * Set the currency in use.
     *
     * @param  string  $currency
     * @return void
     *
     * @throws \Bazar\Exceptions\InvalidCurrencyException
     */
    public static function setCurrency(string $currency): void
    {
        $currency = strtolower($currency);

        if (array_search($currency, static::getCurrencies()) === false) {
            throw new InvalidCurrencyException("The [{$currency}] currency is not registered.");
        }

        static::$currency = $currency;
    }

    /**
     * Register Bazar routes.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function routes(Closure $callback): void
    {
        Route::as('bazar.')
            ->prefix('bazar')
            ->middleware([
                'web',
                'auth',
                'verified',
                'can:manage-bazar',
                HandleInertiaRequests::class,
            ])
            ->group($callback);
    }
}
