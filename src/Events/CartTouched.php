<?php

namespace Bazar\Events;

use Bazar\Contracts\Models\Cart;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartTouched
{
    use Dispatchable, SerializesModels;

    /**
     * The cart instace.
     *
     * @var \Bazar\Contracts\Models\Cart
     */
    public $cart;

    /**
     * Create a new event instance.
     *
     * @param  \Bazar\Contracts\Models\Cart  $cart
     * @return void
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
}
