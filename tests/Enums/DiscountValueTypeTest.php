<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Enums;

use Cone\Bazar\Enums\DiscountValueType;
use Cone\Bazar\Tests\TestCase;

class DiscountValueTypeTest extends TestCase
{
    public function test_discount_value_type_has_fix_case(): void
    {
        $this->assertEquals('fixed_amount', DiscountValueType::FIX->value);
    }

    public function test_discount_value_type_has_percent_case(): void
    {
        $this->assertEquals('percent', DiscountValueType::PERCENT->value);
    }

    public function test_all_cases_are_present(): void
    {
        $cases = DiscountValueType::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(DiscountValueType::FIX, $cases);
        $this->assertContains(DiscountValueType::PERCENT, $cases);
    }
}
