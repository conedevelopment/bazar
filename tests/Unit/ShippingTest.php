<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ShippingFactory;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Tests\TestCase;

class ShippingTest extends TestCase
{
    /** @test */
    public function a_shipping_belongs_to_a_cart()
    {
        $cart = CartFactory::new()->create();
        $shipping = ShippingFactory::new()->make();
        $shipping->shippable()->associate($cart)->save();

        $this->assertSame(
            [Cart::class, $cart->id],
            [$shipping->shippable_type, $shipping->shippable_id]
        );
    }

    /** @test */
    public function a_shipping_belongs_to_an_order()
    {
        $order = $this->admin->orders()->save(OrderFactory::new()->make());
        $shipping = ShippingFactory::new()->make();
        $shipping->shippable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$shipping->shippable_type, $shipping->shippable_id]
        );
    }

    /** @test */
    public function a_shipping_has_address()
    {
        $order = $this->admin->orders()->save(OrderFactory::new()->make());
        $shipping = ShippingFactory::new()->make();
        $shipping->shippable()->associate($order)->save();

        $address = $shipping->address()->save(
            AddressFactory::new()->make()
        );

        $this->assertSame($address->id, $shipping->address->id);
    }
}
