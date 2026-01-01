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
}
