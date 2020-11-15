<?php

namespace Bazar\Contracts\Models;

interface Cart
{
    /**
     * Get the currency attribute.
     *
     * @return string
     */
    public function getCurrencyAttribute(): string;
}
