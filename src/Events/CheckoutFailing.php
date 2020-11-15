<?php

namespace Bazar\Events;

use Bazar\Contracts\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckoutFailing
{
    use Dispatchable, SerializesModels;

    /**
     * The order instace.
     *
     * @var \Bazar\Contracts\Models\Order
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
