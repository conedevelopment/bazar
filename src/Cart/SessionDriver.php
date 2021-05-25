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
        $user = $request->user();

        $cart = Cart::proxy()
                    ->newQuery()
                    ->firstOrCreate(['id' => $request->session()->get('cart_id')])
                    ->setRelation('user', $user)
                    ->loadMissing(['shipping', 'items']);

        if ($user && $cart->user_id !== $user->id) {
            Cart::proxy()->newQuery()->where('user_id', $user->id)->delete();

            $cart->user()->associate($user)->save();
        }

        $request->session()->put('cart_id', $cart->id);

        return $cart;
    }
}
