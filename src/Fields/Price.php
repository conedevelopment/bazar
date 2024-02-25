<?php

namespace Cone\Bazar\Fields;

use Cone\Bazar\Bazar;
use Cone\Bazar\Support\Currency;
use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Price extends Meta
{
    /**
     * The price currency.
     */
    protected string $currency;

    /**
     * Create a new price field instance.
     */
    public function __construct(string $label, ?string $currency = null)
    {
        $this->currency = $currency ?: Bazar::getCurrency();

        parent::__construct($label, sprintf('price_%s', strtolower($this->currency)));

        $this->as(Number::class, function (Number $field): void {
            $field->min(0)
                ->format(function (Request $request, Model $model, mixed $value): ?string {
                    return is_null($value) ? null : (new Currency($value, $this->currency))->format();
                })
                ->suffix(strtoupper($this->currency));
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
