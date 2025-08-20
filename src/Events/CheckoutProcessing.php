<?php

declare(strict_types=1);

namespace Cone\Bazar\Events;

use Cone\Bazar\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;

class CheckoutProcessing
{
    use Dispatchable;

    /**
     * The order instance.
     */
    public Order $order;

    /**
     * The gateway driver.
     */
    public string $driver;

    /**
     * Create a new event instance.
     */
    public function __construct(string $driver, Order $order)
    {
        $this->driver = $driver;
        $this->order = $order;
    }
}
