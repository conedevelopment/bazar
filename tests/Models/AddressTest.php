<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;

class AddressTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_address_belongs_to_user(): void
    {
        $address = Address::factory()->make();

        $address->addressable()->associate($this->user)->save();

        $this->assertSame(
            [get_class($this->user), $this->user->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    public function test_address_belongs_to_cart(): void
    {
        $address = Address::factory()->make();

        $cart = Cart::factory()->create();

        $address->addressable()->associate($cart)->save();

        $this->assertSame(
            [Cart::class, $cart->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    public function test_address_belongs_to_order(): void
    {
        $address = Address::factory()->make();

        $order = Order::factory()->create();

        $address->addressable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    public function test_address_belongs_to_shipping(): void
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

    public function test_address_has_name_attribute(): void
    {
        $address = Address::factory()->make();

        $this->assertSame(
            sprintf('%s %s', $address->first_name, $address->last_name),
            $address->name
        );
    }

    public function test_address_has_alias_attribute(): void
    {
        $address = Address::factory()->make(['alias' => 'Fake']);

        $this->assertSame('Fake', $address->alias);

        $address->alias = null;
        $address->addressable()->associate($this->user);
        $address->save();

        $this->assertSame($address->name, $address->alias);
    }
}
