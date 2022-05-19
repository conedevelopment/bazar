<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;

class CategoryResource extends Resource
{
    /**
     * Define the fields for the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),
            Text::make(__('Name'), 'name'),
        ]);
    }
}
