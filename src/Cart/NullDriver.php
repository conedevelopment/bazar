<?php

declare(strict_types=1);

namespace Cone\Bazar\Cart;

use Cone\Bazar\Models\Cart;
use Illuminate\Http\Request;

class NullDriver extends Driver
{
    /**
     * Resolve the cart for the current request.
     */
    public function resolve(Request $request): Cart
    {
        return Cart::proxy()->newInstance();
    }
}
