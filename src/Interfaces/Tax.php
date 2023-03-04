<?php

namespace Cone\Bazar\Interfaces;

interface Tax
{
    /**
     * Calculate the tax for the given model.
     */
    public function __invoke(Taxable $model): float;
}
