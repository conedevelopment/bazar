<?php

namespace Cone\Bazar\Conversion;

use Cone\Bazar\Models\Medium;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected array $config = [];

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
     * @param  \Cone\Bazar\Models\Medium  $medium
     * @return \Cone\Bazar\Models\Medium
     */
    abstract public function perform(Medium $medium): Medium;
}
