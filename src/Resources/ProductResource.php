<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Fields\Inventory;
use Cone\Bazar\Fields\Price;
use Cone\Bazar\Fields\Variants;
use Cone\Bazar\Models\Product;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\MorphToMany;
use Cone\Root\Fields\Slug;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class ProductResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Product::class;

    /**
     * The group for the resource.
     */
    protected string $group = 'Shop';

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'metaData',
        'propertyValues',
        'propertyValues.property',
    ];

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model::getProxiedClass();
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Name'), 'name')
                ->sortable()
                ->searchable()
                ->rules(['required', 'string', 'max:256']),

            Slug::make(__('Slug'), 'slug')
                ->from(['name']),

            Editor::make(__('Description'), 'description'),

            BelongsToMany::make(__('Category'), 'categories')
                ->display('name'),

            Price::make(__('Price'), Bazar::getCurrency()),

            Inventory::make(),

            BelongsToMany::make(__('Property Values'), 'propertyValues')
                ->with(['property'])
                ->display('name')
                ->hiddenOn(['index'])
                ->groupOptionsBy('property.name'),

            Variants::make(),

            MorphToMany::make(__('Tax Rates'), 'taxRates')
                ->display('name')
                ->hiddenOn(['index']),
        ];
    }
}
