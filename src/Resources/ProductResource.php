<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Fields\Prices;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Json;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ProductResource extends Resource
{
    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Name'), 'name'),

            Editor::make(__('Description'), 'description')->hiddenOnIndex(),

            BelongsToMany::make(__('Categories'), 'categories')->display('name'),

            Json::make(__('Price'), 'prices')
                ->format(static function (Request $request, Model $model): ?string {
                    return $model->formattedPrice;
                })
                ->withFields(array_values(array_map(static function (string $label, string $currency) {
                    return Prices::make(__('Price :currency', ['currency' => $label]), $currency);
                }, Bazar::getCurrencies(), array_keys(Bazar::getCurrencies())))),

            Json::make(__('Inventory'), 'inventory')
                ->hiddenOnDisplay()
                ->withFields([
                    Text::make(__('SKU'), 'sku'),
                    Number::make(__('Quantity'), 'quantity'),
                    Number::make(__('Width'), 'width'),
                    Number::make(__('Height'), 'height'),
                    Number::make(__('Length'), 'length'),
                    Number::make(__('Weight'), 'weight'),
                    Boolean::make(__('Virtual'), 'virtual'),
                    Boolean::make(__('Downloadable'), 'downloadable'),
                ]),
        ];
    }
}
