<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;
use Illuminate\Support\Facades\Date;

class CartTest extends TestCase
{
    protected Cart $cart;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();

        $this->user = User::factory()->create();

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

    public function test_cart_can_belong_to_order(): void
    {
        $order = Order::factory()->create();

        $this->assertFalse($this->cart->order->exists);

        $this->cart->order()->associate($order);

        $this->cart->save();

        $this->assertSame($order->id, $this->cart->order->id);
    }

    public function test_cart_can_belong_to_customer(): void
    {
        $this->assertNull($this->cart->user);

        $this->cart->user()->associate($this->user);

        $this->cart->save();

        $this->assertSame($this->user->id, $this->cart->user->id);
    }

    public function test_cart_has_shipping(): void
    {
        $shipping = $this->cart->shipping()->save(Shipping::factory()->make());

        $this->assertSame($shipping->id, $this->cart->shipping->id);
    }

    public function test_cart_has_address(): void
    {
        $address = $this->cart->address()->save(
            Address::factory()->make()
        );

        $this->assertSame($address->id, $this->cart->address->id);
    }

    public function test_cart_has_total_attribute(): void
    {
        $total = $this->cart->items->sum(function ($item) {
            return ($item->price + $item->tax) * $item->quantity;
        });

        $this->assertEquals($total, $this->cart->total);
    }

    public function test_cart_has_subtotal_attribute(): void
    {
        $total = $this->cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $this->assertEquals($total, $this->cart->subtotal);
    }

    public function test_cart_can_be_locked(): void
    {
        $this->assertFalse($this->cart->locked);
        $this->cart->lock();
        $this->assertTrue($this->cart->locked);
        $this->cart->unlock();
        $this->assertFalse($this->cart->locked);
    }

    public function test_cart_has_query_scopes(): void
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
