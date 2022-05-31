<?php

namespace Cone\Bazar\Fields;

use Cone\Bazar\Models\Item;
use Cone\Root\Fields\MorphMany;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Str;

class Products extends MorphMany
{
    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return [
            Text::make(__('Name'), 'name'),

            Number::make(__('Price'), 'price')
                ->step(0.1)
                ->format(static function (RootRequest $request, Item $item, mixed $value): string {
                    return Str::currency($value, $item->parent->currency);
                }),

            Number::make(__('Tax'), 'tax')
                ->step(0.1)
                ->format(static function (RootRequest $request, Item $item, mixed $value): string {
                    return Str::currency($value, $item->parent->currency);
                }),

            Number::make(__('Quantity'), 'quantity'),
        ];
    }
}
