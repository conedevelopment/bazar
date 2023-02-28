<?php

namespace Cone\Bazar\Models;

use Closure;
use Cone\Bazar\Bazar;
use Cone\Root\Models\Meta;
use Illuminate\Support\Str;

class Price extends Meta
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
    ];

    /**
     * The value formatters.
     *
     * @var array
     */
    protected static array $formatters = [];

    /**
     * Register a formatter to the given currency.
     *
     * @param  string  $currency
     * @param  \Closure  $callback
     * @return void
     */
    public static function formatCurrency(string $currency, Closure $callback): void
    {
        static::$formatters[$currency] = $callback;
    }

    /**
     * Get the currency attribute.
     *
     * @return string
     */
    public function getCurrencyAttribute(): string
    {
        return Str::before(str_replace('price_', '', $this->key), '_');
    }

    /**
     * Get the currency symbol attribute.
     *
     * @return string
     */
    public function getSymbolAttribute(): string
    {
        $currency = $this->currency;

        return Bazar::getCurrencies()[$currency] ?? $currency;
    }

    /**
     * Format the price.
     *
     * @return string
     */
    public function format(): string
    {
        $currency = $this->currency;

        if (isset(static::$formatters[$currency])) {
            return call_user_func_array(static::$formatters[$this->currency], [$this->value, $this->symbol, $currency]);
        }

        return sprintf('%s %s', number_format($this->value, 2, '.', ' '), $this->symbol);
    }
}
