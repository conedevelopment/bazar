<?php

declare(strict_types=1);

namespace Cone\Bazar\Cart;

use Cone\Bazar\Models\Cart;
use Illuminate\Http\Request;

class SessionDriver extends Driver
{
    /**
     * Resolve the cart instance.
     */
    protected function resolve(Request $request): Cart
    {
        return Cart::proxy()
            ->newQuery()
            ->firstOrNew(['id' => $request->session()->get('cart_id')]);
    }

    /**
     * The callback after the cart instance is resolved.
     */
    protected function resolved(Request $request, Cart $cart): void
    {
        parent::resolved($request, $cart);

        $request->session()->put('cart_id', $cart->getKey());
    }
}
