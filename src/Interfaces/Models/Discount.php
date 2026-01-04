<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

interface Discount
{
    /**
     * Get the formatted discount.
     */
    public function format(): string;
}
