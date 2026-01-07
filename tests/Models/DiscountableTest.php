<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Discount;
use Cone\Bazar\Models\Discountable;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class DiscountableTest extends TestCase
{
    protected Order $order;

    protected Discount $discount;

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

        $this->discount = Discount::factory()->create();
    }

    public function test_discountable_pivot_can_be_created(): void
    {
        $this->order->discounts()->attach($this->discount);

        $discountable = Discountable::first();

        $this->assertNotNull($discountable);
    }

    public function test_discountable_uses_correct_table(): void
    {
        $discountable = new Discountable();

        $this->assertSame('bazar_discountables', $discountable->getTable());
    }
}
