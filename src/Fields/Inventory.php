<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\Meta;
use Illuminate\Http\Request;

class Inventory extends Fieldset
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label = null, Closure|string $modelAttribute = null)
    {
        parent::__construct($label ?: __('Inventory'), $modelAttribute ?: 'inventory');
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Meta::make(__('SKU'), 'sku'),

            Meta::make(__('Quantity'), 'quantity')
                ->asNumber(),

            Meta::make(__('Width'), 'width')
                ->asNumber(),

            Meta::make(__('Height'), 'height')
                ->asNumber(),

            Meta::make(__('Length'), 'length')
                ->asNumber(),

            Meta::make(__('Weight'), 'weight')
                ->asNumber(),

            Meta::make(__('Virtual'), 'virtual')
                ->asBoolean(),

            Meta::make(__('Downloadable'), 'downloadable')
                ->asBoolean(),
        ];
    }
}
