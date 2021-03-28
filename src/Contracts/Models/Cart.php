<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Discountable;
use Bazar\Contracts\Itemable;

interface Cart extends Discountable, Itemable
{
    /**
     * Lock the cart.
     *
     * @return void
     */
    public function lock(): void;

    /**
     * Unlock the cart.
     *
     * @return void
     */
    public function unlock(): void;
}
