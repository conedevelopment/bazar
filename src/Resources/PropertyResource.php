<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\Property;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Slug;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class PropertyResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Property::class;

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
                ->rules(['required', 'string', 'max:256'])
                ->searchable()
                ->sortable(),

            Slug::make()
                ->from(['name']),

            HasMany::make(__('Values'), 'values')
                ->asSubResource()
                ->display('name')
                ->withFields(static function () {
                    return [
                        ID::make(),

                        Text::make(__('Name'), 'name')
                            ->required()
                            ->searchable()
                            ->sortable(),

                        Text::make(__('Value'), 'value')
                            ->required()
                            ->searchable()
                            ->sortable(),
                    ];
                }),
        ];
    }
}
