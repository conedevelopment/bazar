<?php

namespace Bazar\Cart;

use Bazar\Contracts\Models\Cart;
use Bazar\Proxies\Cart as CartProxy;
use Illuminate\Support\Facades\Cookie;

class CookieDriver extends Driver
{
    /**
     * Resolve the cart instance.
     *
     * @return \Bazar\Contracts\Models\Cart
     */
    protected function resolve(): Cart
    {
        $cart = CartProxy::query()->firstOrCreate([
            'token' => Cookie::get('cart_token'),
        ]);

        Cookie::queue('cart_token', $cart->token, $this->config['expiration'] ?? 4320);

        return $cart;
    }
}
