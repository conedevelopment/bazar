<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\Category;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Slug;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class CategoryResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Category::class;

    /**
     * The group for the resource.
     */
    protected string $group = 'Shop';

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
                ->required()
                ->rules(['required', 'string', 'max:256']),

            Slug::make()
                ->from(['name']),

            Editor::make(__('Description'), 'description'),
        ];
    }
}
