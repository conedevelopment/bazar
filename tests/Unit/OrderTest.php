<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Address;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Transaction;
use Bazar\Tests\TestCase;

class OrderTest extends TestCase
{
    protected $order, $products;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = factory(Order::class)->create();

        $this->products = factory(Product::class, 3)->create()->mapWithKeys(function ($product) {
            return [$product->id => ['quantity' => mt_rand(1, 5), 'tax' => 0, 'price' => $product->price]];
        });

        $this->order->products()->attach($this->products->all());
    }

    /** @test */
    public function an_order_can_belong_to_a_customer()
    {
        $this->assertNull($this->order->user);

        $this->order->user()->associate($this->user);

        $this->order->save();

        $this->assertSame($this->user->id, $this->order->user_id);
    }

    /** @test */
    public function an_order_has_transactions()
    {
        $transactions = $this->order->transactions()->saveMany(
            factory(Transaction::class, 3)->make()
        );

        $this->assertSame(
            $this->order->transactions->pluck('id')->all(), $transactions->pluck('id')->all()
        );
    }

    /** @test */
    public function a_order_has_address()
    {
        $address = $this->order->address()->save(
            factory(Address::class)->make()
        );

        $this->assertSame($address->id, $this->order->address->id);
    }

    /** @test */
    public function an_order_has_products()
    {
        $product = factory(Product::class)->create();

        $this->order->products()->attach($product, ['price' => 100, 'tax' => 0, 'quantity' => 3]);

        $this->assertTrue(
            $this->order->products->pluck('id')->contains($product->id)
        );
    }

    /** @test */
    public function an_order_has_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return ($product['price'] + $product['tax']) * $product['quantity'];
        });

        $total -= $this->order->discount;

        $this->assertEquals($total, $this->order->total);
    }

    /** @test */
    public function an_order_has_net_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return $product['price'] * $product['quantity'];
        });

        $total -= $this->order->discount;

        $this->assertEquals($total, $this->order->netTotal);
    }
}
