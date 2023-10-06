<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\Product;
use Cone\Root\Columns\Column;
use Cone\Root\Columns\ID;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class ProductResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Product::class;

    /**
     * Define the fields.
     */
    public function columns(Request $request): array
    {
        return [
            ID::make(),
            Column::make(__('Name'), 'name'),
        ];
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [];
    }
}
