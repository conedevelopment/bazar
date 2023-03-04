<?php

namespace Cone\Bazar\Cart;

use Cone\Bazar\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieDriver extends Driver
{
    /**
     * Resolve the cart instance.
     */
    protected function resolve(Request $request): Cart
    {
        return Cart::proxy()
                    ->newQuery()
                    ->firstOrNew(['id' => $request->cookie('cart_id')]);
    }

    /**
     * The callback after the cart instance is resolved.
     */
    protected function resolved(Request $request, Cart $cart): void
    {
        parent::resolved($request, $cart);

        Cookie::queue('cart_id', $cart->id, $this->config['expiration'] ?? 4320);
    }
}
