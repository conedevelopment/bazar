<?php

namespace Cone\Bazar\Events;

use Cone\Bazar\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckoutProcessing
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The order instace.
     *
     * @var \Cone\Bazar\Models\Order
     */
    public Order $order;

    /**
     * Create a new event instance.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
