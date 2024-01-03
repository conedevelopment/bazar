<?php

namespace Cone\Bazar\Events;

use Cone\Bazar\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;

class CheckoutProcessed
{
    use Dispatchable;

    /**
     * The order instance.
     */
    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
