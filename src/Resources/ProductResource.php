<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Fields\Prices;
use Cone\Bazar\Models\Product;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\Media;
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
     * Define the columns.
     */
    public function columns(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Name'), 'name')
                ->sortable()
                ->searchable(),
        ];
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return array_merge(parent::fields($request), [
            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:256']),

            Slug::make(__('Slug'), 'slug')
                ->from(['name']),

            Editor::make(__('Description'), 'description'),

            BelongsToMany::make(__('Categories'), 'categories')
                ->display('name'),

            Prices::make(),

            Media::make(__('Cover'), 'media', 'media'),
        ]);
    }
}
