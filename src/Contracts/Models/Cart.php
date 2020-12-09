<?php

namespace Bazar\Contracts\Models;

interface Cart
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
