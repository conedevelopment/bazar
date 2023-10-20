<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\Category;
use Cone\Root\Columns\Column;
use Cone\Root\Columns\ID;
use Cone\Root\Fields\Slug;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class CategoryResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Category::class;

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
        return [
            Slug::make(__('Slug'), 'slug')
                ->from(['name']),
        ];
    }
}
