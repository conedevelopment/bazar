<?php

namespace Bazar\Contracts\Shipping;

interface Manager
{
    /**
     * Get all the shipping methods.
     *
     * @return array
     */
    public function methods(): array;

    /**
     * Determine if the given method exists.
     *
     * @param  string  $method
     * @return bool
     */
    public function has(string $method): bool;
}
