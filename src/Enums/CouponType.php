<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

enum CouponType: string
{
    case FIX = 'fixed';
    case PERCENT = 'percent';
}
