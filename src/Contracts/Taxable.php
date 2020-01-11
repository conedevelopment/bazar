<?php

namespace Bazar\Contracts;

interface Taxable
{
    /**
     * Calculate the tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function tax(bool $update = true): float;
}
