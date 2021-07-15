<?php

namespace Cone\Bazar\Contracts\Conversion;

use Cone\Bazar\Conversion\GdDriver;
use Closure;

interface Manager
{
    /**
     * Register a new conversion.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public function registerConversion(string $name, Closure $callback): void;

    /**
     * Remove the given conversion.
     *
     * @param  string  $name
     * @return void
     */
    public function removeConversion(string $name): void;

    /**
     * Get all the registered conversions.
     *
     * @return array
     */
    public function getConversions(): array;

    /**
     * Create the GD driver.
     *
     * @return \Cone\Bazar\Conversion\GdDriver
     */
    public function createGdDriver(): GdDriver;
}
