<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\MorphToMany;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;

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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Text::make(__('Total'), 'formatted_total')
                ->visibleOnDisplay(),

            Date::make(__('Created at'), 'created_at')
                ->visibleOnDisplay(),

            BelongsTo::make(__('Customer'), 'user', 'user')
                ->nullable()
                ->async()
                ->display('name'),

            MorphToMany::make(__('Products'), 'items', 'items')
                    ->hiddenOnIndex()
                    ->display('name')
                    ->withFields([
                        Number::make(__('Price'), 'price'),
                        Number::make(__('Tax'), 'tax'),
                        Number::make(__('Quantity'), 'quantity'),
                    ]),
        ]);
    }
}
