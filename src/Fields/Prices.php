<?php

namespace Cone\Bazar\Fields;

use Cone\Bazar\Bazar;
use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Arr;

class Prices extends Meta
{
    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label = null, string $name = 'meta_data')
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
                    Number::make(__('Price :currency', ['currency' => $symbol]), 'price_'.$currency)
                        ->rules(['required']),
                    Number::make(__('Sale Price :currency', ['currency' => $symbol]), 'sale_price_'.$currency),
                ];
            }, Bazar::getCurrencies(), array_keys(Bazar::getCurrencies())))
        );
    }
}
