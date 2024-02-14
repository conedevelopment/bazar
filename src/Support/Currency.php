<?php

namespace Cone\Bazar\Support;

use Closure;
use Cone\Bazar\Bazar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use NumberFormatter;
use Stringable;

class Currency implements JsonSerializable, Stringable
{
    use Macroable;

    /**
     * The currency value.
     */
    protected int|float $value;

    /**
     * The currency.
     */
    protected string $currency;

    /**
     * The currency locale.
     */
    protected string $locale = 'en';

    /**
     * The currency precision.
     */
    protected ?int $precision = null;

    /**
     * The currency formatter.
     */
    protected static ?Closure $formatter = null;

    /**
     * Create a new currency instance.
     */
    public function __construct(int|float $value, ?string $currency = null, ?int $precision = null, ?string $locale = null)
    {
        $this->value = $value;
        $this->currency = $currency ?: Bazar::getCurrency();
        $this->precision = $precision ?: Config::get('bazar.currencies.available.'.$currency.'.precision', 2);
        $this->locale = $locale ?: App::getLocale();
    }

    /**
     * Set the formatter callback.
     */
    public static function formatUsing(Closure $callback): void
    {
        static::$formatter = $callback;
    }

    /**
     * Format the currency.
     */
    public function format(): string
    {
        $formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);

        if (! is_null($this->precision)) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $this->precision);
        }

        if (! is_null(static::$formatter)) {
            call_user_func_array(static::$formatter, [$formatter, $this->value, $this->currency]);
        }

        return $formatter->formatCurrency($this->value, $this->currency);
    }

    /**
     * Get the JSON serializable format of the currency.
     */
    public function jsonSerialize(): mixed
    {
        return $this->format();
    }

    /**
     * Convert the currency to string.
     */
    public function __toString(): string
    {
        return $this->format();
    }
}
