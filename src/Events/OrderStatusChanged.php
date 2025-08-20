<?php

declare(strict_types=1);

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
     * The new order status.
     */
    public string $to;

    /**
     * The old order status.
     */
    public string $from;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $to, string $from)
    {
        $this->order = $order;
        $this->to = $to;
        $this->from = $from;
    }
}
