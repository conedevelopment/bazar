<?php

declare(strict_types=1);

namespace Cone\Bazar\Cart;

use Cone\Bazar\Models\Cart;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class QueryStringDriver extends Driver
{
    /**
     * Resolve the cart for the current request.
     */
    public function resolve(Request $request): Cart
    {
        return Cart::proxy()
            ->where('bazar_carts.uuid', $request->query($this->config['key'] ?? 'cart'))
            ->firstOrFail();
    }

    /**
     * The callback after the cart instance is resolved.
     */
    protected function resolved(Request $request, Cart $cart): void
    {
        if (! is_null($cart->user_id) && $cart->user_id !== $request->user()?->getKey()) {
            throw new AuthorizationException(__('You are not authorized to access this cart.'));
        }

        parent::resolved($request, $cart);
    }
}
