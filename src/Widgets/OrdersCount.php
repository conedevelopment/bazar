<?php

namespace Cone\Bazar\Widgets;

use Cone\Bazar\Models\Order;
use Cone\Root\Widgets\Value;
use Illuminate\Database\Eloquent\Builder;

class OrdersCount extends Value
{
    /**
     * The widget icon.
     */
    protected ?string $icon = 'archive';

    /**
     * Create a new Eloquent query.
     */
    public function query(): Builder
    {
        return Order::query()->status(Order::FULFILLED);
    }
}
