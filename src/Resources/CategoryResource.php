<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class CategoryResource extends Resource
{
    /**
     * Define the fields for the resource.
     */
    public function fields(Request $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),
            Text::make(__('Name'), 'name'),
        ]);
    }
}
