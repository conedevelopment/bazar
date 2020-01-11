<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Shipping;
use Bazar\Tests\TestCase;

class ShippingTest extends TestCase
{
    /** @test */
    public function a_shipping_belongs_to_a_cart()
    {
        $cart = factory(Cart::class)->create();
        $shipping = factory(Shipping::class)->make();
        $shipping->shippable()->associate($cart)->save();

        $this->assertSame(
            [Cart::class, $cart->id],
            [$shipping->shippable_type, $shipping->shippable_id]
        );
    }

    /** @test */
    public function a_shipping_belongs_to_an_order()
    {
        $order = $this->admin->orders()->save(factory(Order::class)->make());
        $shipping = factory(Shipping::class)->make();
        $shipping->shippable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$shipping->shippable_type, $shipping->shippable_id]
        );
    }

    /** @test */
    public function a_shipping_has_address()
    {
        $order = $this->admin->orders()->save(factory(Order::class)->make());
        $shipping = factory(Shipping::class)->make();
        $shipping->shippable()->associate($order)->save();

        $address = $shipping->address()->save(
            factory(Address::class)->make()
        );

        $this->assertSame($address->id, $shipping->address->id);
    }
}
