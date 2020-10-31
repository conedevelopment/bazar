<?php

namespace Bazar\Cart;

use Bazar\Models\Cart;
use Illuminate\Support\Facades\Cookie;

class CookieDriver extends Driver
{
    /**
     * The cookie expiration in minutes.
     *
     * @var string
     */
    public const EXPIRATION_MINUTES = 4320;

    /**
     * Resolve the cart instance.
     *
     * @return \Bazar\Models\Cart
     */
    protected function resolve(): Cart
    {
        $cart = Cart::query()->firstOrCreate([
            'token' => Cookie::get('cart_token'),
        ]);

        Cookie::queue('cart_token', $cart->token, static::EXPIRATION_MINUTES);

        return $cart;
    }
}
