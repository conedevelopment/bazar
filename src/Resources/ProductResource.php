<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Fields\Inventory;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Media;
use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Filters\TrashStatus;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;
use Illuminate\Support\Arr;

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
     * Define the filters for the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function filters(RootRequest $request): array
    {
        return array_merge(parent::filters($request), [
            TrashStatus::make(),
        ]);
    }

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
                ->display('name')
                ->searchable()
                ->async(),

            Media::make(__('Media'), 'media')
                ->display('name')
                ->hiddenOnIndex(),

            Meta::make(__('Prices'))
                ->withFields(Arr::flatten(array_map(static function (string $symbol, string $currency): array {
                    return [
                        Number::make(__('Price :currency', ['currency' => $symbol]), 'price_'.$currency)
                            ->rules(['required']),
                        Number::make(__('Sale Price :currency', ['currency' => $symbol]), 'sale_price_'.$currency),
                    ];
                }, Bazar::getCurrencies(), array_keys(Bazar::getCurrencies())))),

            Meta::make(__('Inventory'))->withFields([
                Text::make(__('SKU'), 'sku'),
                Number::make(__('Quantity'), 'quantity'),
                Number::make(__('Width'), 'width'),
                Number::make(__('Height'), 'height'),
                Number::make(__('Length'), 'length'),
                Number::make(__('Weight'), 'weight'),
                Boolean::make(__('Virtual'), 'virtual'),
                Boolean::make(__('Downloadable'), 'downloadable'),
            ]),

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
