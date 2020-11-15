<?php

namespace Bazar\Contracts\Repositories;

use Bazar\Contracts\Models\Medium;
use Closure;

interface ConversionRepository
{
    /**
     * Register a new conversion.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public function register(string $name, Closure $callback): void;

    /**
     * Remove the given conversion.
     *
     * @param  string  $name
     * @return void
     */
    public function remove(string $name): void;

    /**
     * Perform the registered conversion on the given medium.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return \Bazar\Contracts\Models\Medium
     */
    public function perform(Medium $medium): Medium;
}
