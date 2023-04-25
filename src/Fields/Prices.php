<?php

namespace Cone\Bazar\Fields;

use Cone\Bazar\Bazar;
use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Arr;

class Prices extends Fieldset
{
    /**
     * Create a new prices field instance.
     */
    public function __construct(string $label = null, string $name = null)
    {
        parent::__construct($label ?: __('Prices'), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(
            parent::fields($request),
            Arr::flatten(array_map(static function (string $symbol, string $currency): array {
                return [
                    Meta::make(__('Price :currency', ['currency' => $symbol]), 'price_'.$currency)
                        ->asNumber(function (Number $field): void {
                            $field->rules(['required', 'numeric', 'max:1300']);
                        }),
                    Meta::make(__('Sale Price :currency', ['currency' => $symbol]), 'sale_price_'.$currency)
                        ->asNumber(),
                ];
            }, Bazar::getCurrencies(), array_keys(Bazar::getCurrencies())))
        );
    }
}
