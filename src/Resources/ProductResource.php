<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Fields\Inventory;
use Cone\Bazar\Fields\Prices;
use Cone\Bazar\Fields\Properties;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Country;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
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
            // Editor::make(__('Description'), 'description')->hiddenOnIndex(),
            Prices::make(__('Price'), 'prices'),
            Properties::make(__('Properties'), 'properties')->hiddenOnIndex(),
            Inventory::make(__('Inventory'), 'inventory')->hiddenOnIndex(),
            BelongsToMany::make(__('Categories'), 'categories')->display('name'),
        ];
    }
}
