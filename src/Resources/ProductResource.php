<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Fields\Inventory;
use Cone\Bazar\Fields\Prices;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Json;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
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

            Text::make(__('Name'), 'name'),

            Editor::make(__('Description'), 'description')
                ->withMedia()
                ->hiddenOnIndex(),

            BelongsToMany::make(__('Categories'), 'categories')
                ->asSubResource()
                ->display('name'),

            Json::make(__('Price'), 'prices')
                ->format(static function (RootRequest $request, Model $model): ?string {
                    return $model->formattedPrice;
                })
                ->withFields(array_values(array_map(static function (string $label, string $currency) {
                    return Prices::make(__('Price :currency', ['currency' => $label]), $currency);
                }, Bazar::getCurrencies(), array_keys(Bazar::getCurrencies())))),

            Inventory::make(__('Inventory'), 'inventory')
                ->hiddenOnDisplay(),

            HasMany::make(__('Variants'), 'variants')
                ->asSubResource()
                ->hiddenOnDisplay()
                ->display('alias')
                ->withFields([
                    Text::make(__('Alias'), 'alias')->rules(['required']),

                    Json::make(__('Price'), 'prices')
                        ->format(static function (RootRequest $request, Model $model): ?string {
                            return $model->formattedPrice;
                        })
                        ->withFields(array_values(array_map(static function (string $label, string $currency) {
                            return Prices::make(__('Price :currency', ['currency' => $label]), $currency);
                        }, Bazar::getCurrencies(), array_keys(Bazar::getCurrencies())))),

                    Inventory::make(__('Inventory'), 'inventory')
                        ->hiddenOnDisplay(),
                ]),
        ]);
    }
}
