<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Models\Item;
use Cone\Root\Fields\MorphMany;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;

class Products extends MorphMany
{
    /**
     * Create a new products field instance.
     */
    public function __construct(string $label = null, string $name = null, Closure|string $relation = null)
    {
        parent::__construct(
            $label ?: __('Products'), $name ?: 'items', $relation
        );

        $this->asSubResource();
        $this->hiddenOnIndex();
        $this->display('name');
    }

    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return [
            Text::make(__('Name'), 'name'),

            Currency::make(__('Price'), 'price')
                ->step(0.1)
                ->currency(static function (RootRequest $request, Item $item): string {
                    return $item->parent->currency;
                }),

            Currency::make(__('Tax'), 'tax')
                ->step(0.1)
                ->currency(static function (RootRequest $request, Item $item): string {
                    return $item->parent->currency;
                }),

            Number::make(__('Quantity'), 'quantity'),
        ];
    }
}
