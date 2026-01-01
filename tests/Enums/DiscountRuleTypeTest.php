<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Enums;

use Cone\Bazar\Enums\DiscountRuleType;
use Cone\Bazar\Tests\TestCase;

class DiscountRuleTypeTest extends TestCase
{
    public function test_discount_rule_type_has_cart_case(): void
    {
        $this->assertEquals('cart', DiscountRuleType::CART->value);
    }

    public function test_discount_rule_type_has_buyable_case(): void
    {
        $this->assertEquals('buyable', DiscountRuleType::BUYABLE->value);
    }

    public function test_discount_rule_type_has_shipping_case(): void
    {
        $this->assertEquals('shipping', DiscountRuleType::SHIPPING->value);
    }

    public function test_cart_type_has_highest_priority(): void
    {
        $this->assertEquals(3, DiscountRuleType::CART->priority());
    }

    public function test_buyable_type_has_medium_priority(): void
    {
        $this->assertEquals(2, DiscountRuleType::BUYABLE->priority());
    }

    public function test_shipping_type_has_lowest_priority(): void
    {
        $this->assertEquals(1, DiscountRuleType::SHIPPING->priority());
    }

    public function test_cart_type_has_correct_label(): void
    {
        $this->assertEquals('Cart Total', DiscountRuleType::CART->label());
    }

    public function test_buyable_type_has_correct_label(): void
    {
        $this->assertEquals('Buyable Item', DiscountRuleType::BUYABLE->label());
    }

    public function test_shipping_type_has_correct_label(): void
    {
        $this->assertEquals('Shipping', DiscountRuleType::SHIPPING->label());
    }

    public function test_priorities_are_ordered_correctly(): void
    {
        $this->assertGreaterThan(
            DiscountRuleType::BUYABLE->priority(),
            DiscountRuleType::CART->priority()
        );

        $this->assertGreaterThan(
            DiscountRuleType::SHIPPING->priority(),
            DiscountRuleType::BUYABLE->priority()
        );
    }
}
