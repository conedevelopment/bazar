<?php

namespace Cone\Bazar\Tests\Repositories;

use Cone\Bazar\Interfaces\Discount as Contract;
use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Support\Facades\Discount;
use Cone\Bazar\Tests\TestCase;

class DiscountTest extends TestCase
{
    protected Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();

        Product::factory()->count(2)->create()->each(function ($product) {
            $this->cart->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'price' => $product->price,
                'quantity' => 1,
                'name' => $product->name,
            ]);
        });

        Discount::register('custom-30', 30);
    }

    public function test_discount_repository_calculates_discounts(): void
    {
        Discount::register('custom-object', new CustomDiscount);
        Discount::register('custom-closure', function (Discountable $model) {
            return 100;
        });

        $this->assertEquals(230, $this->cart->calculateDiscount());
    }

    public function test_discount_repository_removes_discounts(): void
    {
        Discount::remove('custom-30');

        $this->assertEquals(0, $this->cart->calculateDiscount());
    }

    public function test_discount_repository_disables_discounts(): void
    {
        $this->assertEquals(30, $this->cart->calculateDiscount());

        Discount::disable();

        Discount::register('custom-10', 10);

        $this->assertEquals(30, $this->cart->calculateDiscount());

        Discount::enable();

        $this->assertEquals(40, $this->cart->calculateDiscount());
    }
}

class CustomDiscount implements Contract
{
    public function __invoke(Discountable $model): float
    {
        return 100;
    }
}
