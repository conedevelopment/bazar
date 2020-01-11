<?php

namespace Bazar\Contracts\Gateway;

interface Manager
{
    /**
     * Get all the payment gateways.
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
