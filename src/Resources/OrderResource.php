<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Order;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\Computed;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Select;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

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
    public function fields(Request $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Computed::make(__('Total'), static function (Request $request, Order $order): string {
                return $order->formattedTotal;
            }),

            Date::make(__('Created at'), 'created_at'),

            BelongsTo::make(__('Customer'), 'user')
                ->nullable()
                ->display('name'),

            Select::make(__('Currency'), 'currency')
                ->options(Bazar::getCurrencies()),
        ]);
    }

    /**
     * Define the relations for the resource.
     */
    public function relations(Request $request): array
    {
        return array_merge(parent::relations($request), [
            //
        ]);
    }
}
