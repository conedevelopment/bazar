<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class CouponTest extends TestCase
{
    protected Coupon $coupon;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coupon = Coupon::factory()->create();

        $this->order = Order::factory()->create();

        Product::factory()->count(3)->create()->each(function ($product) {
            $this->order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => mt_rand(1, 5),
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_coupon_can_have_rules(): void
    {
        $this->coupon->rules = ['limit' => 2];

        $this->coupon->save();

        $this->assertSame(2, $this->coupon->limit());
    }

    public function test_invalid_coupon_cannot_be_applied_on_checkoutable_model(): void
    {
        $this->assertSame(0.0, $this->order->getDiscount());
        $this->order->applyCoupon('FOO-BAR');
        $this->order->refresh();
        $this->assertSame(0.0, $this->order->getDiscount());
    }

    public function test_coupon_can_be_applied_on_checkoutable_model(): void
    {
        $this->assertSame(0.0, $this->order->getDiscount());

        $this->order->applyCoupon($this->coupon->code);

        $this->order->refresh();

        $this->assertTrue($this->order->coupons->contains($this->coupon));

        $this->assertSame(
            $this->coupon->calculate($this->order),
            $this->order->getDiscount()
        );
    }

    public function test_coupon_has_query_scopes(): void
    {
        $this->assertSame(
            'select * from "bazar_coupons" where "bazar_coupons"."active" = ?',
            Coupon::query()->active()->toSql()
        );

        $this->assertSame(
            'select * from "bazar_coupons" where ("bazar_coupons"."active" = ? and "bazar_coupons"."expires_at" is null or "bazar_coupons"."expires_at" > ?)',
            Coupon::query()->available()->toSql()
        );

        $this->assertSame(
            'select * from "bazar_coupons" where lower(`bazar_coupons`.`code`) like ?',
            Coupon::query()->code('TEST')->toSql()
        );
    }
}
