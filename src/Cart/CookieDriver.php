<?php

namespace Bazar\Cart;

use Bazar\Models\Cart;
use Illuminate\Support\Facades\Cookie;

class CookieDriver extends Driver
{
    /**
     * Resolve the cart instance.
     *
     * @return \Bazar\Models\Cart
     */
    protected function resolve(): Cart
    {
        $cart = Cart::firstOrCreate([
            'token' => Cookie::get('cart_token'),
        ]);

        Cookie::queue('cart_token', $cart->token, 864000);

        return $cart;
    }
}
