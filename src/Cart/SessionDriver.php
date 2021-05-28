<?php

namespace Bazar\Cart;

use Bazar\Models\Cart;
use Illuminate\Http\Request;

class SessionDriver extends Driver
{
    /**
     * Resolve the cart instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Models\Cart
     */
    protected function resolve(Request $request): Cart
    {
        return Cart::proxy()
                    ->newQuery()
                    ->firstOrNew(['id' => $request->session()->get('cart_id')]);
    }

    /**
     * The callback after the cart instance is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Models\Cart
     */
    protected function resolved(Request $request, Cart $cart): void
    {
        parent::resolved($request, $cart);

        $request->session()->put('cart_id', $cart->id);
    }
}
