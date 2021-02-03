<?php

namespace Bazar\Contracts\Conversion;

use Bazar\Conversion\GdDriver;

interface Manager
{
    /**
     * Create the GD driver.
     *
     * @return \Bazar\Conversion\GdDriver
     */
    public function createGdDriver(): GdDriver;
}
