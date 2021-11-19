<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\ID;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class CategoryResource extends Resource
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
        ];
    }
}
