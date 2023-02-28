<?php

namespace Cone\Bazar\Interfaces;

interface Tax
{
    /**
     * Calculate the tax for the given model.
     *
     * @param  \Cone\Bazar\Interfaces\Taxable  $model
     */
    public function calculate(Taxable $model): float;
}
