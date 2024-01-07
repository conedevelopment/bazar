<?php

namespace Cone\Bazar\Events;

use Cone\Bazar\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;

class OrderStatusChanged
{
    use Dispatchable;

    /**
     * The order instance.
     */
    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(ORder $order)
    {
        $this->order = $order;
    }
}
