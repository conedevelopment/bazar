<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

interface AppliedCoupon
{
    /**
     * Get the formatted coupon value.
     */
    public function format(): string;
}
