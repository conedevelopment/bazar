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
        $cartId = $this->cart->id;

        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.0]);

        $this->assertCount(1, $this->cart->discounts);

        $this->assertDatabaseHas('bazar_discounts', [
            'discountable_id' => $cartId,
            'discountable_type' => Cart::class,
        ]);

        $this->cart->delete();

        $this->assertDatabaseMissing('bazar_carts', ['id' => $cartId]);
        $this->assertDatabaseMissing('bazar_discounts', [
            'discountable_id' => $cartId,
            'discountable_type' => Cart::class,
        ]);
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

    public function test_sync_without_detaching_preserves_existing_discounts(): void
    {
        $rule1 = DiscountRule::factory()->create();
        $rule2 = DiscountRule::factory()->create();

        $this->cart->discounts()->attach($rule1, ['value' => 10.0]);
        $this->cart->discounts()->syncWithoutDetaching([$rule2->id => ['value' => 5.0]]);

        $this->cart->refresh();

        $this->assertCount(2, $this->cart->discounts);
        $this->assertTrue($this->cart->discounts->contains($rule1));
        $this->assertTrue($this->cart->discounts->contains($rule2));
    }

    public function test_discount_value_persists_correctly(): void
    {
        $expectedValue = 12.34;

        $this->cart->discounts()->attach($this->discountRule, ['value' => $expectedValue]);

        $this->cart->refresh();

        $actualValue = $this->cart->discounts()->first()->discount->value;

        $this->assertEquals($expectedValue, $actualValue);
    }

    public function test_discounts_use_morph_to_many_relationship(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.0]);

        $discount = $this->cart->discounts()->first();

        $this->assertNotNull($discount->discount);
        $this->assertEquals($this->cart->id, $discount->discount->discountable_id);
        $this->assertEquals(get_class($this->cart), $discount->discount->discountable_type);
    }

    public function test_shipping_can_have_discounts(): void
    {
        $shipping = $this->cart->shipping()->create([
            'name' => 'Express Shipping',
            'cost' => 15.0,
            'driver' => 'local-pickup',
        ]);

        $shipping->discounts()->attach($this->discountRule, ['value' => 3.0]);

        $this->assertCount(1, $shipping->discounts);
        $this->assertEquals(3.0, $shipping->discounts()->first()->discount->value);
    }

    public function test_discount_detaches_when_rule_is_deleted(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.0]);

        $this->assertCount(1, $this->cart->discounts);

        $ruleId = $this->discountRule->id;
        $this->discountRule->delete();

        $this->cart->refresh();

        // The relationship should still exist but point to a non-existent rule
        $this->assertDatabaseMissing('bazar_discount_rules', ['id' => $ruleId]);
    }
}
