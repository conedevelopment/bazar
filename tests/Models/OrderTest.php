<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Tests\TestCase;

class OrderTest extends TestCase
{
    protected Order $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();

        Product::factory()->count(3)->create()->each(function ($product) {
            $this->order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => mt_rand(1, 5),
                'tax' => 0,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_order_can_belong_to_customer(): void
    {
        $this->assertNull($this->order->user);

        $this->order->user()->associate($this->user);

        $this->order->save();

        $this->assertSame($this->user->id, $this->order->user_id);
    }

    public function test_order_has_transactions(): void
    {
        $transactions = $this->order->transactions()->saveMany(
            Transaction::factory()->count(3)->make()
        );

        $this->assertSame(
            $this->order->transactions->pluck('id')->all(), $transactions->pluck('id')->all()
        );
    }

    public function test_order_can_have_cart(): void
    {
        $cart = $this->order->cart()->save(
            Cart::factory()->make()
        );

        $this->assertSame($cart->id, $this->order->cart->id);
    }

    public function test_order_has_address(): void
    {
        $address = $this->order->address()->save(
            Address::factory()->make()
        );

        $this->assertSame($address->id, $this->order->address->id);
    }

    public function test_order_has_total_attribute(): void
    {
        $total = $this->order->items->sum(function ($item) {
            return ($item->price + $item->tax) * $item->quantity;
        });

        $total -= $this->order->discount;

        $this->assertEquals($total, $this->order->total);
    }

    public function test_order_has_net_total_attribute(): void
    {
        $total = $this->order->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $total -= $this->order->discount;

        $this->assertEquals($total, $this->order->netTotal);
    }

    public function test_order_has_query_scopes(): void
    {
        $this->assertSame(
            $this->order->newQuery()->where('bazar_orders.status', 'pending')->toSql(),
            $this->order->newQuery()->status('pending')->toSql()
        );

        $this->assertSame(
            $this->order->newQuery()->whereHas('user', function ($q) {
                $q->where('users.id', 1);
            })->toSql(),
            $this->order->newQuery()->user(1)->toSql()
        );
    }
}
