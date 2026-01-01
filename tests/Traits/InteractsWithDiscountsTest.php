<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Traits;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\DiscountRule;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class InteractsWithDiscountsTest extends TestCase
{
    protected Cart $cart;

    protected Item $item;

    protected DiscountRule $discountRule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();

        $product = Product::factory()->create();

        $this->item = $this->cart->items()->create([
            'buyable_id' => $product->id,
            'buyable_type' => Product::class,
            'quantity' => 2,
            'price' => $product->price,
            'name' => $product->name,
        ]);

        $this->discountRule = DiscountRule::factory()->create();
    }

    public function test_discountable_has_discounts_relationship(): void
    {
        $this->assertTrue(method_exists($this->cart, 'discounts'));
        $this->assertTrue(method_exists($this->item, 'discounts'));
    }

    public function test_discounts_can_be_attached_to_cart(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.0]);

        $this->assertCount(1, $this->cart->discounts);
        $this->assertTrue($this->cart->discounts->contains($this->discountRule));
    }

    public function test_discounts_can_be_attached_to_item(): void
    {
        $this->item->discounts()->attach($this->discountRule, ['value' => 5.0]);

        $this->assertCount(1, $this->item->discounts);
        $this->assertTrue($this->item->discounts->contains($this->discountRule));
    }

    public function test_multiple_discounts_can_be_attached(): void
    {
        $rule1 = DiscountRule::factory()->create();
        $rule2 = DiscountRule::factory()->create();

        $this->cart->discounts()->attach($rule1, ['value' => 10.0]);
        $this->cart->discounts()->attach($rule2, ['value' => 5.0]);

        $this->assertCount(2, $this->cart->discounts);
    }

    public function test_discounts_are_detached_on_model_deletion(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.0]);

        $this->assertCount(1, $this->cart->discounts);

        $cartId = $this->cart->id;
        $this->cart->delete();

        // Verify the discount relationship is removed
        $cart = Cart::withTrashed()->find($cartId);
        if ($cart) {
            $this->assertCount(0, $cart->discounts);
        }
    }

    public function test_discount_pivot_has_value(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 25.0]);

        $discount = $this->cart->discounts()->first();

        $this->assertEquals(25.0, $discount->discount->value);
    }

    public function test_discount_relationship_uses_custom_pivot(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 15.0]);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertInstanceOf(\Cone\Bazar\Models\Discount::class, $discount);
    }
}
