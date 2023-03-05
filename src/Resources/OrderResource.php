<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Fields\Currency;
use Cone\Bazar\Fields\Products;
use Cone\Bazar\Fields\Shipping;
use Cone\Bazar\Fields\Transactions;
use Cone\Bazar\Models\Order;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\Computed;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Select;
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

            Computed::make(__('Total'), static function (RootRequest $request, Order $order): string {
                return $order->formattedTotal;
            }),

            Date::make(__('Created at'), 'created_at')
                ->visibleOnDisplay(),

            BelongsTo::make(__('Customer'), 'user')
                ->nullable()
                ->async()
                ->display('name'),

            Select::make(__('Currency'), 'currency')
                ->options(Bazar::getCurrencies()),

            Currency::make(__('Discount'), 'discount')
                ->step(0.1)
                ->currency(static function (RootRequest $request, Order $model): string {
                    return $model->currency;
                }),

            Shipping::make(),

            Products::make(),

            Transactions::make(),
        ]);
    }
}
