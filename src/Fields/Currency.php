<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Bazar;
use Cone\Root\Fields\Number;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Currency extends Number
{
    /**
     * The currency resolver.
     */
    protected ?Closure $currencyResolver = null;

    /**
     * Create a new field instance.
     *
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->currencyResolver = static function (): string {
            return Bazar::getCurrency();
        };
    }

    /**
     * Set the currency resolver property.
     *
     * @return $this
     */
    public function currency(Closure|string $value): static
    {
        if (is_string($value)) {
            $value = static function () use ($value): string {
                return $value;
            };
        }

        $this->currencyResolver = $value;

        return $this;
    }

    /**
     * Resolve the currency.
     */
    public function resolveCurrency(RootRequest $request, Model $model): string
    {
        return call_user_func_array($this->currencyResolver, [$request, $model]);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $currency = $this->resolveCurrency($request, $model);

            $this->formatResolver = function (RootRequest $request, Model $model, mixed $value) use ($currency): mixed {
                return Str::currency($value, $currency);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
