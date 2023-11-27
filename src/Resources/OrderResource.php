<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Fields\Items;
use Cone\Bazar\Fields\OrderStatus;
use Cone\Bazar\Fields\Transactions;
use Cone\Bazar\Models\Order;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class OrderResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Order::class;

    /**
     * The group for the resource.
     */
    protected string $group = 'Shop';

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'user',
    ];

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            BelongsTo::make(__('Customer'), 'user')
                ->display('name')
                ->sortable(column: 'name'),

            Text::make(__('Total'), static function (Request $request, Order $model): string {
                return $model->formattedTotal;
            }),

            Select::make(__('Currency'), 'currency')
                ->options(Bazar::getCurrencies())
                ->hiddenOn(['index']),

            OrderStatus::make(),

            Date::make(__('Created At'), 'created_at')
                ->withTime()
                ->sortable(),

            Items::make(),

            Transactions::make(),
        ];
    }
}
