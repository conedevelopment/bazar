<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Enums\DiscountType;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\DiscountRule;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;

class DiscountRuleTest extends TestCase
{
    protected DiscountRule $discountRule;

    protected Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discountRule = DiscountRule::factory()->create([
            'rules' => [
                ['value' => 1, 'type' => DiscountType::FIX->value, 'discount' => 10, 'currency' => null],
            ],
        ]);

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

    public function test_discount_rule_has_default_attributes(): void
    {
        $this->assertTrue($this->discountRule->active);
        $this->assertFalse($this->discountRule->stackable);
        $this->assertIsArray($this->discountRule->rules);
    }

    public function test_discount_rule_can_have_rules(): void
    {
        $this->discountRule->rules = ['min_amount' => 100];
        $this->discountRule->save();

        $this->assertSame(['min_amount' => 100], $this->discountRule->rules);
    }

    public function test_discount_rule_can_be_associated_with_users(): void
    {
        $users = User::factory()->count(2)->create();

        $this->discountRule->users()->attach($users);

        $this->assertCount(2, $this->discountRule->users);
        $this->assertTrue($this->discountRule->users->contains($users[0]));
        $this->assertTrue($this->discountRule->users->contains($users[1]));
    }

    public function test_discount_rule_can_calculate_discount(): void
    {
        $value = $this->discountRule->calculate($this->cart);

        $this->assertEquals(10.0, $value);
    }

    public function test_discount_rule_can_be_applied_to_discountable(): void
    {
        $this->assertCount(0, $this->cart->discounts);

        $this->discountRule->discountable_type = Cart::class;
        $this->discountRule->save();

        $this->discountRule->apply($this->cart);

        $this->cart->refresh();

        $this->assertCount(1, $this->cart->discounts);
        $this->assertTrue($this->cart->discounts->contains($this->discountRule));
    }

    public function test_discount_rule_can_be_applied_to_item(): void
    {
        $item = $this->cart->items->first();

        $this->assertCount(0, $item->discounts);

        $this->discountRule->discountable_type = $item->buyable_type;
        $this->discountRule->save();

        $this->discountRule->apply($item);

        $item->refresh();

        $this->assertCount(1, $item->discounts);
        $this->assertTrue($item->discounts->contains($this->discountRule));
    }

    public function test_discount_rule_applies_to_shipping(): void
    {
        $shipping = $this->cart->shipping()->create([
            'name' => 'Standard Shipping',
            'fee' => 30.0,
            'driver' => 'local-pickup',
        ]);

        $this->discountRule->discountable_type = Shipping::class;
        $this->discountRule->save();

        $this->assertCount(0, $shipping->discounts);

        $this->discountRule->apply($shipping);

        $shipping->refresh();

        $this->assertCount(1, $shipping->discounts);
        $this->assertTrue($shipping->discounts->contains($this->discountRule));
    }

    public function test_multiple_discount_rules_can_be_applied(): void
    {
        $rule1 = DiscountRule::factory()->create([
            'stackable' => true,
            'discountable_type' => Cart::class,
            'rules' => [
                ['value' => 1, 'type' => DiscountType::FIX->value, 'discount' => 10, 'currency' => null],
            ],
        ]);
        $rule2 = DiscountRule::factory()->create([
            'stackable' => true,
            'discountable_type' => Cart::class,
            'rules' => [
                ['value' => 1, 'type' => DiscountType::FIX->value, 'discount' => 10, 'currency' => null],
            ],
        ]);

        $rule1->apply($this->cart);
        $rule2->apply($this->cart);

        $this->cart->refresh();

        $this->assertCount(2, $this->cart->discounts);
        $this->assertTrue($this->cart->discounts->contains($rule1));
        $this->assertTrue($this->cart->discounts->contains($rule2));
    }

    public function test_discount_rule_can_be_inactive(): void
    {
        $inactiveRule = DiscountRule::factory()->create(['active' => false]);

        $this->assertFalse($inactiveRule->active);
    }

    public function test_discount_rule_stackable_attribute(): void
    {
        $stackableRule = DiscountRule::factory()->create(['stackable' => true]);
        $nonStackableRule = DiscountRule::factory()->create(['stackable' => false]);

        $this->assertTrue($stackableRule->stackable);
        $this->assertFalse($nonStackableRule->stackable);
    }

    public function test_discount_rule_relationship_uses_correct_table(): void
    {
        $this->discountRule->discountable_type = Cart::class;
        $this->discountRule->save();

        $this->discountRule->apply($this->cart);

        $this->assertDatabaseHas('bazar_discounts', [
            'discount_rule_id' => $this->discountRule->id,
            'discountable_id' => $this->cart->id,
            'discountable_type' => get_class($this->cart),
        ]);
    }
}
