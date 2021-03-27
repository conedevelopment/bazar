<?php

namespace Bazar\Tests\Unit;

use Bazar\Bazar;
use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\ShippingFactory;
use Bazar\Tests\TestCase;
use Illuminate\Support\Carbon;

class CartTest extends TestCase
{
    protected $cart, $products;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = CartFactory::new()->create();

        $this->products = ProductFactory::new()->count(3)->create()->mapWithKeys(function ($product) {
            [$quantity, $tax, $price] = [mt_rand(1, 5), 0, $product->price('sale') ?: $product->price()];

            return [$product->id => compact('price', 'tax', 'quantity')];
        });

        $this->cart->products()->attach($this->products->all());
    }

    /** @test */
    public function it_can_belong_to_a_customer()
    {
        $this->assertNull($this->cart->user);

        $this->cart->user()->associate($this->user);

        $this->cart->save();

        $this->assertSame($this->user->id, $this->cart->user->id);
    }

    /** @test */
    public function it_has_a_shipping()
    {
        $shipping = $this->cart->shipping()->save(ShippingFactory::new()->make());

        $this->assertSame($shipping->id, $this->cart->shipping->id);
    }

    /** @test */
    public function it_has_address()
    {
        $address = $this->cart->address()->save(
            AddressFactory::new()->make()
        );

        $this->assertSame($address->id, $this->cart->address->id);
    }

    /** @test */
    public function it_has_products()
    {
        $product = ProductFactory::new()->create();

        $this->cart->products()->attach($product, [
            'price' => 100, 'tax' => 0, 'quantity' => 3,
        ]);

        $this->assertTrue(
            $this->cart->products->pluck('id')->contains($product->id)
        );
    }

    /** @test */
    public function it_has_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return ($product['price'] + $product['tax']) * $product['quantity'];
        });

        $total -= $this->cart->discount;

        $this->assertEquals($total, $this->cart->total);
    }

    /** @test */
    public function it_has_net_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return $product['price'] * $product['quantity'];
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
                ->where('bazar_carts.updated_at', '<', Carbon::now()->subDays(3))
                ->toSql(),
            $this->cart->newQuery()->expired()->toSql()
        );
    }
}
