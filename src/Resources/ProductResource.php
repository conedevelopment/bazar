<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Fields\Inventory;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\Computed;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Json;
use Cone\Root\Fields\Media;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;

class ProductResource extends Resource
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
    */
    protected array $with = [
        'metas',
    ];

    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Text::make(__('Name'), 'name')->sortable(),

            Editor::make(__('Description'), 'description')
                ->withMedia()
                ->hiddenOnIndex(),

            BelongsToMany::make(__('Categories'), 'categories')
                ->asSubResource()
                ->display('name'),

            Media::make(__('Media'), 'media')
                ->display('name')
                ->hiddenOnIndex(),

            Json::make(__('Prices'), 'prices')
                ->withFields([
                    Number::make(__('Price'), 'metas.price')
                        ->rules(['required']),
                    Number::make(__('Sale Price'), 'metas.sale_price'),
                ]),

            // Text::make(__('SKU'), 'metas.sku'),
            // Number::make(__('Quantity'), 'metas.quantity'),
            // Number::make(__('Width'), 'metas.width'),
            // Number::make(__('Height'), 'metas.height'),
            // Number::make(__('Length'), 'metas.length'),
            // Number::make(__('Weight'), 'metas.weight'),
            // Boolean::make(__('Virtual'), 'metas.virtual'),
            // Boolean::make(__('Downloadable'), 'metas.downloadable'),

            HasMany::make(__('Variants'), 'variants')
                ->asSubResource()
                ->hiddenOnDisplay()
                ->display('alias')
                ->withFields([
                    Text::make(__('Alias'), 'alias')->rules(['required']),

                    Inventory::make(__('Inventory'), 'inventory')
                        ->hiddenOnDisplay(),

                    Media::make(__('Media')),
                ]),
        ]);
    }
}
