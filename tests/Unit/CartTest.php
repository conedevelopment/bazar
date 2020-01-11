<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Product;
use Bazar\Models\Shipping;
use Bazar\Tests\TestCase;

class CartTest extends TestCase
{
    protected $cart, $products;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = factory(Cart::class)->create();

        $this->products = factory(Product::class, 3)->create()->mapWithKeys(function ($product) {
            [$quantity, $tax, $price] = [mt_rand(1, 5), 0, $product->price];

            return [$product->id => compact('price', 'tax', 'quantity')];
        });

        $this->cart->products()->attach($this->products->all());
    }

    /** @test */
    public function a_cart_can_belong_to_a_customer()
    {
        $this->assertNull($this->cart->user);

        $this->cart->user()->associate($this->user);

        $this->cart->save();

        $this->assertSame($this->user->id, $this->cart->user->id);
    }

    /** @test */
    public function a_cart_has_a_shipping()
    {
        $shipping = $this->cart->shipping()->save(factory(Shipping::class)->make());

        $this->assertSame($shipping->id, $this->cart->shipping->id);
    }

    /** @test */
    public function a_cart_has_address()
    {
        $address = $this->cart->address()->save(
            factory(Address::class)->make()
        );

        $this->assertSame($address->id, $this->cart->address->id);
    }

    /** @test */
    public function a_cart_has_products()
    {
        $product = factory(Product::class)->create();

        $this->cart->products()->attach($product, [
            'price' => 100, 'tax' => 0, 'quantity' => 3,
        ]);

        $this->assertTrue(
            $this->cart->products->pluck('id')->contains($product->id)
        );
    }

    /** @test */
    public function a_cart_has_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return ($product['price'] + $product['tax']) * $product['quantity'];
        });

        $total -= $this->cart->discount;

        $this->assertEquals($total, $this->cart->total);
    }

    /** @test */
    public function a_cart_has_net_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return $product['price'] * $product['quantity'];
        });

        $total -= $this->cart->discount;

        $this->assertEquals($total, $this->cart->netTotal);
    }
}
