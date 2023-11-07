<?php

namespace Cone\Bazar\Fields;

use Cone\Bazar\Bazar;
use Cone\Root\Fields\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Price extends Meta
{
    /**
     * The price currency.
     */
    protected string $currency;

    /**
     * Create a new price field instance.
     */
    public function __construct(string $label, string $modelAttribute = null, string $currency = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->currency = $currency ?: Bazar::getCurrency();
    }

    /**
     * Set the currency attribute.
     */
    public function currency(string $value): static
    {
        $this->currency = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                return is_null($value) ? null : Str::currency($value, $this->currency);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
