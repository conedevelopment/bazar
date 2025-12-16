<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Tests\TestCase;

class CouponTest extends TestCase
{
    protected Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coupon = Coupon::factory()->create();
    }

    public function test_coupon_can_have_rules(): void
    {
        $this->coupon->rules = ['limit' => 2];

        $this->coupon->save();

        $this->assertSame(2, $this->coupon->limit());
    }

    public function test_a_coupon_can_be_applied_on_a_checkoutable_model(): void
    {
        $this->assertTrue(true);
    }
}
