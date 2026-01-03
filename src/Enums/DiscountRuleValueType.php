<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

use Cone\Root\Enums\Arrayable;

enum DiscountRuleValueType: string
{
    use Arrayable;

    case TOTAL = 'total';
    case QUANTITY = 'quantity';

    /**
     * Get the label of the target.
     */
    public function label(): string
    {
        return match ($this) {
            self::TOTAL => __('Total'),
            self::QUANTITY => __('Quantity'),
        };
    }
}
