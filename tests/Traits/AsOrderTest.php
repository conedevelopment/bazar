<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Traits;

use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class AsOrderTest extends TestCase
{
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();

        Product::factory()->count(2)->create()->each(function ($product) {
            $this->order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => 2,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_order_has_items(): void
    {
        $this->assertCount(2, $this->order->items);
    }

    public function test_order_has_shipping(): void
    {
        $this->assertNotNull($this->order->shipping);
    }

    public function test_order_calculates_total(): void
    {
        $total = $this->order->getTotal();

        $this->assertIsFloat($total);
        $this->assertGreaterThan(0, $total);
    }

    public function test_order_calculates_subtotal(): void
    {
        $subtotal = $this->order->getSubtotal();

        $this->assertIsFloat($subtotal);
        $this->assertGreaterThan(0, $subtotal);
    }

    public function test_order_calculates_tax(): void
    {
        $this->order->calculateTax();

        $tax = $this->order->getTax();

        $this->assertIsFloat($tax);
        $this->assertGreaterThanOrEqual(0, $tax);
    }

    public function test_order_can_apply_coupon(): void
    {
        $coupon = Coupon::factory()->create();

        $result = $this->order->applyCoupon($coupon);

        $this->assertTrue($result);
        $this->assertTrue($this->order->coupons->contains($coupon));
    }

    public function test_order_can_remove_coupon(): void
    {
        $coupon = Coupon::factory()->create();

        $this->order->applyCoupon($coupon);
        $this->order->removeCoupon($coupon);

        $this->order->refresh();

        $this->assertFalse($this->order->coupons->contains($coupon));
    }

    public function test_order_formats_total(): void
    {
        $formatted = $this->order->getFormattedTotal();

        $this->assertIsString($formatted);
    }

    public function test_order_determines_if_needs_payment(): void
    {
        $needsPayment = $this->order->needsPayment();

        $this->assertIsBool($needsPayment);
    }

    public function test_order_has_currency(): void
    {
        $currency = $this->order->getCurrency();

        $this->assertNotNull($currency);
    }
}
