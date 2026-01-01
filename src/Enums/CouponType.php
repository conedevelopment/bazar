<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

use Cone\Root\Enums\Arrayable;

enum CouponType: string
{
    use Arrayable;

    case FIX = 'fix';
    case PERCENT = 'percent';

    /**
     * Get the label.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIX => __('Fixed Amount'),
            self::PERCENT => __('Percentage'),
        };
    }
}
