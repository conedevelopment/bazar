<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\AppliedCoupon;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class AppliedCouponTest extends TestCase
{
    protected Cart $cart;

    protected Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
        $this->coupon = Coupon::factory()->create(['code' => 'TEST']);

        Product::factory()->count(2)->create()->each(function ($product) {
            $this->cart->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => 2,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_applied_coupon_belongs_to_coupon(): void
    {
        $this->cart->applyCoupon($this->coupon);

        $appliedCoupon = AppliedCoupon::first();

        $this->assertInstanceOf(Coupon::class, $appliedCoupon->coupon);
        $this->assertSame($this->coupon->id, $appliedCoupon->coupon->id);
    }

    public function test_applied_coupon_belongs_to_couponable(): void
    {
        $this->cart->applyCoupon($this->coupon);

        $appliedCoupon = AppliedCoupon::first();

        $this->assertInstanceOf(Cart::class, $appliedCoupon->couponable);
        $this->assertSame($this->cart->id, $appliedCoupon->couponable->id);
    }

    public function test_applied_coupon_has_value(): void
    {
        $this->cart->applyCoupon($this->coupon);

        $appliedCoupon = AppliedCoupon::first();

        $this->assertIsFloat($appliedCoupon->value);
        $this->assertGreaterThanOrEqual(0, $appliedCoupon->value);
    }

    public function test_applied_coupon_formats_value(): void
    {
        $this->cart->applyCoupon($this->coupon);

        $appliedCoupon = AppliedCoupon::first();

        $formatted = $appliedCoupon->format();

        $this->assertIsString($formatted);
    }
}
