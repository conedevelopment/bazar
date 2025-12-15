<?php

declare(strict_types=1);

namespace Cone\Bazar\Fields;

use Cone\Bazar\Bazar;
use Cone\Bazar\Enums\Currency;
use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Price extends Meta
{
    /**
     * The price currency.
     */
    protected Currency $currency;

    /**
     * Create a new price field instance.
     */
    public function __construct(string $label, ?Currency $currency = null)
    {
        $this->currency = $currency ?: Bazar::getCurrency();

        parent::__construct($label, sprintf('price_%s', $this->currency->key()));

        $this->aggregateResolver = null;

        $this->as(Number::class, function (Number $field): void {
            $field->min(0)
                ->format(function (Request $request, Model $model, mixed $value): ?string {
                    return match (true) {
                        is_null($value) => null,
                        default => $model->checkoutable->getCurrency()->format((float) ($value ?? 0)),
                    };
                })
                ->suffix($this->currency->value);
        });
    }

    /**
     * Set the currency attribute.
     */
    public function currency(Currency $value): static
    {
        $this->currency = $value;

        $this->modelAttribute = sprintf('price_%s', $value->key());

        return $this;
    }
}
