<?php

namespace Bazar\Contracts\Shipping;

interface Manager
{
    /**
     * Get all drivers.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get the enabled drivers.
     *
     * @return array
     */
    public function enabled(): array;

    /**
     * Get the disabled drivers.
     *
     * @return array
     */
    public function disabled(): array;

    /**
     * Determine if the given driver exists.
     *
     * @param  string  $driver
     * @return bool
     */
    public function has(string $driver): bool;
}
