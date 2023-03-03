<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Tests\TestCase;

class AddressTest extends TestCase
{
    /** @test */
    public function an_address_belongs_to_user()
    {
        $address = Address::factory()->make();

        $address->addressable()->associate($this->user)->save();

        $this->assertSame(
            [get_class($this->user), $this->user->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_cart()
    {
        $address = Address::factory()->make();

        $cart = Cart::factory()->create();

        $address->addressable()->associate($cart)->save();

        $this->assertSame(
            [Cart::class, $cart->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_order()
    {
        $address = Address::factory()->make();

        $order = Order::factory()->create();

        $address->addressable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function an_address_belongs_to_shipping()
    {
        $address = Address::factory()->make();

        $order = Order::factory()->create();

        $shipping = $order->shipping()->save(
            Shipping::factory()->make()
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
        $address = Address::factory()->make();

        $this->assertSame(
            sprintf('%s %s', $address->first_name, $address->last_name),
            $address->name
        );
    }

    /** @test */
    public function an_address_has_alias_attribute()
    {
        $address = Address::factory()->make(['alias' => 'Fake']);

        $this->assertSame('Fake', $address->alias);

        $address->alias = null;
        $address->addressable()->associate($this->user);
        $address->save();

        $this->assertSame("#{$address->id}", $address->alias);
    }
}
