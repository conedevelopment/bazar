<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Bazar;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;

class Variants extends HasMany
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'metaData',
        'propertyValues',
        'propertyValues.property',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string $modelAttribute = 'variants', Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Variants'), $modelAttribute, $relation);

        $this->display('alias');
        $this->asSubResource();
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Alias'), 'alias'),

            Price::make(__('Price'), Bazar::getCurrency()),

            VariantProperties::make(__('Properties'), 'propertyValues'),

            Editor::make(__('Description'), 'description'),

            Inventory::make(),
        ];
    }
}
