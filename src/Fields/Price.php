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
    public function __construct(string $label, string $currency = null)
    {
        $this->currency = $currency ?: Bazar::getCurrency();

        parent::__construct($label, sprintf('price_%s', strtolower($this->currency)));

        $this->asNumber();

        $this->field->min(0);

        $this->field->format(function (Request $request, Model $model, mixed $value): ?string {
            return is_null($value) ? null : Str::currency($value, $this->currency);
        });
    }

    /**
     * Set the currency attribute.
     */
    public function currency(string $value): static
    {
        $this->currency = $value;

        $this->modelAttribute = sprintf('price_%s', strtolower($value));

        return $this;
    }
}
