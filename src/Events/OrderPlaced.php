<?php

namespace Bazar\Events;

use Bazar\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable, SerializesModels;

    /**
     * The order instace.
     *
     * @var \Bazar\Models\Order
     */
    public Order $order;

    /**
     * Create a new event instance.
     *
     * @param  \Bazar\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
