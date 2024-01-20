<?php

namespace Cone\Bazar\Models;

use Closure;
use Cone\Bazar\Bazar;
use Cone\Root\Models\Meta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Price extends Meta
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'float',
    ];

    /**
     * The value formatters.
     */
    protected static array $formatters = [];

    /**
     * Register a formatter to the given currency.
     */
    public static function formatCurrency(string $currency, Closure $callback): void
    {
        static::$formatters[$currency] = $callback;
    }

    /**
     * Get the currency attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function currency(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                return Str::before(str_replace('price_', '', $attributes['key']), '_');
            }
        );
    }

    /**
     * Get the currency symbol attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function symbol(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                return Bazar::getCurrencies()[$attributes['currency']] ?? $attributes['currency'];
            }
        );
    }

    /**
     * Format the price.
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
