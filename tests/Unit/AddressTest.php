<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ShippingFactory;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Shipping;
use Bazar\Support\Countries;
use Bazar\Tests\TestCase;

class AddressTest extends TestCase
{
    /** @test */
    public function an_address_belongs_to_a_customer()
    {
        $address = AddressFactory::new()->make();

        $address->addressable()->associate($this->user)->save();

        $this->assertSame(
            [get_class($this->user), $this->user->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_a_cart()
    {
        $address = AddressFactory::new()->make();

        $cart = CartFactory::new()->create();

        $address->addressable()->associate($cart)->save();

        $this->assertSame(
            [Cart::class, $cart->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_an_order()
    {
        $address = AddressFactory::new()->make();

        $order = OrderFactory::new()->create();

        $address->addressable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_an_sihpping()
    {
        $address = AddressFactory::new()->make();

        $order = OrderFactory::new()->create();

        $shipping = $order->shipping()->save(
            ShippingFactory::new()->make()
        );

        $address->addressable()->associate($shipping)->save();

        $this->assertSame(
            [Shipping::class, $shipping->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_has_name_attribute()
    {
        $address = AddressFactory::new()->make();

        $this->assertSame(
            sprintf('%s %s', $address->first_name, $address->last_name),
            $address->name
        );
    }

    /** @test */
    public function an_address_has_country_name_attribute()
    {
        $address = AddressFactory::new()->make();

        $this->assertSame(
            Countries::name($address->country), $address->countryName
        );
    }
}
