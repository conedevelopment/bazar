<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Date;

class CartTest extends TestCase
{
    protected $cart;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();

        Product::factory()->count(3)->create()->each(function ($product) {
            $this->cart->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => mt_rand(1, 5),
                'tax' => 0,
                'price' => $product->getPrice('sale') ?: $product->getPrice(),
                'name' => $product->name,
            ]);
        });
    }

    /** @test */
    public function it_can_belong_to_order()
    {
        $order = Order::factory()->create();

        $this->assertFalse($this->cart->order->exists);

        $this->cart->order()->associate($order);

        $this->cart->save();

        $this->assertSame($order->id, $this->cart->order->id);
    }

    /** @test */
    public function it_can_belong_to_customer()
    {
        $this->assertNull($this->cart->user);

        $this->cart->user()->associate($this->user);

        $this->cart->save();

        $this->assertSame($this->user->id, $this->cart->user->id);
    }

    /** @test */
    public function it_has_shipping()
    {
        $shipping = $this->cart->shipping()->save(Shipping::factory()->make());

        $this->assertSame($shipping->id, $this->cart->shipping->id);
    }

    /** @test */
    public function it_has_address()
    {
        $address = $this->cart->address()->save(
            Address::factory()->make()
        );

        $this->assertSame($address->id, $this->cart->address->id);
    }

    /** @test */
    public function it_has_total_attribute()
    {
        $total = $this->cart->items->sum(function ($item) {
            return ($item->price + $item->tax) * $item->quantity;
        });

        $total -= $this->cart->discount;

        $this->assertEquals($total, $this->cart->total);
    }

    /** @test */
    public function it_has_net_total_attribute()
    {
        $total = $this->cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $total -= $this->cart->discount;

        $this->assertEquals($total, $this->cart->netTotal);
    }

    /** @test */
    public function it_can_be_locked()
    {
        $this->assertFalse($this->cart->locked);
        $this->cart->lock();
        $this->assertTrue($this->cart->locked);
        $this->cart->unlock();
        $this->assertFalse($this->cart->locked);
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->cart->newQuery()->where('bazar_carts.locked', true)->toSql(),
            $this->cart->newQuery()->locked()->toSql()
        );

        $this->assertSame(
            $this->cart->newQuery()->where('bazar_carts.locked', false)->toSql(),
            $this->cart->newQuery()->unlocked()->toSql()
        );

        $this->assertSame(
            $this->cart->newQuery()
                ->whereNull('bazar_carts.user_id')
                ->where('bazar_carts.updated_at', '<', Date::now()->subDays(3))
                ->toSql(),
            $this->cart->newQuery()->expired()->toSql()
        );
    }
}
