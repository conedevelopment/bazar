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
