<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

use Cone\Root\Enums\Arrayable;

enum DiscountValueType: string
{
    use Arrayable;

    case FIX = 'fixed_amount';
    case PERCENT = 'percent';

    /**
     * Get the label of the target.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIX => __('Fixed Amount'),
            self::PERCENT => __('Percentage'),
        };
    }
}
