<?php

namespace Cone\Bazar\Widgets;

use Cone\Bazar\Models\Order;
use Cone\Root\Widgets\Value;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrdersCount extends Value
{
    /**
     * The widget icon.
     */
    protected ?string $icon = 'archive';

    /**
     * {@inheritdoc}
     */
    public function resolveQuery(Request $request): Builder
    {
        $this->queryResolver = fn (): Builder => Order::query()->status(Order::FULFILLED);

        return parent::resolveQuery($request);
    }
}
