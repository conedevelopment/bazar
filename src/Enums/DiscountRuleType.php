<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

use Cone\Root\Enums\Arrayable;

enum DiscountRuleType: string
{
    use Arrayable;

    case CART = 'cart';
    case BUYABLE = 'buyable';
    case SHIPPING = 'shipping';

    /**
     * Get the priority of the target.
     */
    public function priority(): int
    {
        return match ($this) {
            self::CART => 3,
            self::BUYABLE => 2,
            self::SHIPPING => 1,
        };
    }

    /**
     * Get the label of the target.
     */
    public function label(): string
    {
        return match ($this) {
            self::CART => __('Cart Total'),
            self::BUYABLE => __('Buyable Item'),
            self::SHIPPING => __('Shipping'),
        };
    }
}
