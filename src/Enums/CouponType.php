<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

enum CouponType: string
{
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

    /**
     * Convert to array.
     */
    public static function toArray(): array
    {
        return array_reduce(self::cases(), function (array $cases, self $case): array {
            return array_merge(
                $cases,
                [$case->value => $case->label()]
            );
        }, []);
    }
}
