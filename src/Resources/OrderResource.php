<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\Order;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\ID;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class OrderResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Order::class;

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            BelongsTo::make(__('Customer'), 'user')
                ->display('name'),
        ];
    }
}
