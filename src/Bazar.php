<?php

namespace Bazar;

use Bazar\Exceptions\InvalidCurrencyException;
use Bazar\Http\Middleware\ComponentMiddleware;
use Bazar\Http\Middleware\ShareComponentData;
use Closure;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

abstract class Bazar
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '0.4.5';

    /**
     * The default currency.
     *
     * @var string|null
     */
    protected static $currency = null;

    /**
     * Get the version.
     *
     * @return string
     */
    public static function version(): string
    {
        return static::VERSION;
    }

    /**
     * Get the asset version.
     *
     * @return string|null
     */
    public static function assetVersion(): ?string
    {
        $path = public_path('mix-manifest.json');

        return is_file($path) ? md5_file($path) : null;
    }

    /**
     * Get all the currencies.
     *
     * @return array
     */
    public static function currencies(): array
    {
        return Config::get('bazar.currencies.available', []);
    }

    /**
     * Get or set the currency in use.
     *
     * @param  string|null  $currency
     * @return string
     *
     * @throws \Bazar\Exceptions\InvalidCurrencyException
     */
    public static function currency(string $currency = null): string
    {
        if (is_null($currency)) {
            $currency = static::$currency ?: Config::get('bazar.currencies.default', 'usd');
        }

        if (! in_array($currency, array_keys(static::currencies()))) {
            throw new InvalidCurrencyException("The [{$currency}] currency is not registered.");
        }

        return static::$currency = $currency;
    }

    /**
     * Register Bazar routes.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function routes(Closure $callback): void
    {
        Route::as('bazar.')->prefix('bazar')->middleware([
            'web',
            'auth',
            'verified',
            'can:manage-bazar',
            ComponentMiddleware::class,
            ShareComponentData::class,
        ])->group(static function (Router $router) use ($callback): void {
            $callback($router);
        });
    }
}
