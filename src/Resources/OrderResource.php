<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\Date;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class OrderResource extends Resource
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected array $with = [
        'items',
        'shipping',
    ];

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
            Text::make(__('Total'), 'formatted_total')->visibleOnIndex(),
            Date::make(__('Created at'), 'created_at')->visibleOnIndex(),
        ];
    }
}
