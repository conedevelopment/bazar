<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Conversion\Manager as Contract;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
    /**
     * The registered conversions.
     *
     * @var array
     */
    protected $conversions = [];

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.media.conversion.default');
    }

    /**
     * Create the GD driver.
     *
     * @return \Bazar\Conversion\GdDriver
     */
    public function createGdDriver(): GdDriver
    {
        return new GdDriver(array_merge(
            $this->config->get('bazar.media.conversion.drivers.gd', []),
            ['conversions' => $this->conversions]
        ));
    }
}
