<?php

namespace Bazar\Contracts;

interface Tax
{
    /**
     * Calculate the tax for the given model.
     *
     * @param  \Bazar\Contracts\Taxable  $model
     * @return float
     */
    public function calculate(Taxable $model): float;
}
