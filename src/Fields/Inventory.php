<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Illuminate\Http\Request;

class Inventory extends Fieldset
{
    /**
     * Create a new field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = 'inventory')
    {
        parent::__construct($label ?: __('Inventory'), $modelAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Meta::make(__('SKU'), 'sku'),

            Meta::make(__('Quantity'), 'quantity')
                ->as(Number::class),

            Meta::make(__('Width'), 'width')
                ->as(Number::class),

            Meta::make(__('Height'), 'height')
                ->as(Number::class),

            Meta::make(__('Length'), 'length')
                ->as(Number::class),

            Meta::make(__('Weight'), 'weight')
                ->as(Number::class),

            Meta::make(__('Virtual'), 'virtual')
                ->as(Boolean::class),

            Meta::make(__('Downloadable'), 'downloadable')
                ->as(Boolean::class),
        ];
    }
}
