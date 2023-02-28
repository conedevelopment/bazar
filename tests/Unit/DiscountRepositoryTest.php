<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Interfaces\Discount as Contract;
use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Support\Facades\Discount;
use Cone\Bazar\Tests\TestCase;

class DiscountRepositoryTest extends TestCase
{
    protected $cart;

    public function setUp(): void
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

    /** @test */
    public function it_can_calculate_discounts()
    {
        Discount::register('custom-object', new CustomDiscount);
        Discount::register('custom-class', CustomDiscount::class);
        Discount::register('not-a-discount', new class
        {
            public function calculate(Discountable $model)
            {
                return 100;
            }
        });
        Discount::register('custom-closure', function (Discountable $model) {
            return 100;
        });

        $this->assertEquals(330, $this->cart->calculateDiscount());
    }

    /** @test */
    public function it_can_remove_discounts()
    {
        Discount::remove('custom-30');

        $this->assertEquals(0, $this->cart->calculateDiscount());
    }

    /** @test */
    public function it_can_disable_discounts()
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
    public function calculate(Discountable $model): float
    {
        return 100;
    }
}
