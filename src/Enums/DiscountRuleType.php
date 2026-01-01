<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

enum DiscountRuleType: string
{
    case FIX = 'fixed_amount';
    case PERCENT = 'percent';
}
