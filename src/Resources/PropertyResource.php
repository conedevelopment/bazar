<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Slug;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;

class PropertyResource extends Resource
{
    /**
     * Define the fields for the resource.
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Text::make(__('Name'), 'name'),

            Slug::make(__('Slug'), 'slug'),

            HasMany::make(__('Values'), 'values')
                ->display('name')
                ->asSubResource()
                ->withFields([
                    Text::make(__('Name'), 'name'),
                    Text::make(__('Value'), 'value'),
                ]),
        ]);
    }
}
