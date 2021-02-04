<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Perform the registered conversions on the medium.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return \Bazar\Contracts\Models\Medium
     */
    abstract public function perform(Medium $medium): Medium;
}
