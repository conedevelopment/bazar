<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Discount;
use Cone\Bazar\Models\DiscountRule;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class DiscountTest extends TestCase
{
    protected DiscountRule $discountRule;

    protected Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discountRule = DiscountRule::factory()->create();

        $this->cart = Cart::factory()->create();

        Product::factory(3)->create()->each(function ($product) {
            $this->cart->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => mt_rand(1, 5),
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_discount_has_default_value(): void
    {
        $this->discountRule->apply($this->cart);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertInstanceOf(Discount::class, $discount);
        $this->assertEquals(0.0, $discount->value);
    }

    public function test_discount_value_can_be_set(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 25.50]);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertEquals(25.50, $discount->value);
    }

    public function test_discount_can_be_formatted(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.00]);

        $discount = $this->cart->discounts()->first()->discount;

        $formatted = $discount->format();

        $this->assertIsString($formatted);
    }

    public function test_discount_has_formatted_value_attribute(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 15.00]);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertIsString($discount->formatted_value);
    }

    public function test_discount_value_is_cast_to_float(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => '20.50']);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertIsFloat($discount->value);
        $this->assertEquals(20.50, $discount->value);
    }

    public function test_discount_has_timestamps(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.00]);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertNotNull($discount->created_at);
        $this->assertNotNull($discount->updated_at);
    }

    public function test_discount_uses_correct_table(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 5.00]);

        $this->assertDatabaseHas('bazar_discounts', [
            'discount_rule_id' => $this->discountRule->id,
            'discountable_id' => $this->cart->id,
            'value' => 5.00,
        ]);
    }

    public function test_multiple_discounts_on_same_model(): void
    {
        $rule1 = DiscountRule::factory()->create();
        $rule2 = DiscountRule::factory()->create();

        $this->cart->discounts()->attach($rule1, ['value' => 10.00]);
        $this->cart->discounts()->attach($rule2, ['value' => 5.00]);

        $discounts = $this->cart->discounts;

        $this->assertCount(2, $discounts);
        $this->assertEquals(10.00, $discounts->where('id', $rule1->id)->first()->discount->value);
        $this->assertEquals(5.00, $discounts->where('id', $rule2->id)->first()->discount->value);
    }

    public function test_discount_can_be_updated(): void
    {
        $this->cart->discounts()->attach($this->discountRule, ['value' => 10.00]);

        $this->cart->discounts()->updateExistingPivot($this->discountRule->id, ['value' => 20.00]);

        $discount = $this->cart->discounts()->first()->discount;

        $this->assertEquals(20.00, $discount->value);
    }
}
