<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Fields\Products;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\HasOne;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;

class OrderResource extends Resource
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'items',
        'shipping',
        'transactions',
    ];

    /**
     * Define the fields for the resource.
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Text::make(__('Total'), 'formatted_total')
                ->visibleOnDisplay(),

            Date::make(__('Created at'), 'created_at')
                ->visibleOnDisplay(),

            BelongsTo::make(__('Customer'), 'user')
                ->nullable()
                ->async()
                ->display('name'),

            HasOne::make(__('Shipping'), 'shipping')
                ->asSubResource()
                ->display('driver_name'),

            Products::make(__('Products'), 'items')
                ->asSubResource()
                ->hiddenOnIndex()
                ->display('name'),

            HasMany::make(__('Transactions'), 'transactions')
                ->asSubResource()
                ->hiddenOnIndex()
                ->display('driver_name'),
        ]);
    }
}
