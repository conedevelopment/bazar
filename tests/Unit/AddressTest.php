<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Address;
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
        $address = factory(Address::class)->make();

        $address->addressable()->associate($this->user)->save();

        $this->assertSame(
            [get_class($this->user), $this->user->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_a_cart()
    {
        $address = factory(Address::class)->make();

        $cart = factory(Cart::class)->create();

        $address->addressable()->associate($cart)->save();

        $this->assertSame(
            [Cart::class, $cart->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_an_order()
    {
        $address = factory(Address::class)->make();

        $order = factory(Order::class)->create();

        $address->addressable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_an_sihpping()
    {
        $address = factory(Address::class)->make();

        $order = factory(Order::class)->create();
        $shipping = $order->shipping()->save(
            factory(Shipping::class)->make()
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
        $address = factory(Address::class)->make();

        $this->assertSame(
            sprintf('%s %s', $address->first_name, $address->last_name),
            $address->name
        );
    }

    /** @test */
    public function an_address_has_country_name_attribute()
    {
        $address = factory(Address::class)->make();

        $this->assertSame(
            Countries::name($address->country), $address->countryName
        );
    }
}
