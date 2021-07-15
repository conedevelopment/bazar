<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Contracts\Breadcrumbable;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Countries;
use Cone\Bazar\Tests\TestCase;

class AddressTest extends TestCase
{
    /** @test */
    public function it_belongs_to_user()
    {
        $address = Address::factory()->make();

        $address->addressable()->associate($this->user)->save();

        $this->assertSame(
            [get_class($this->user), $this->user->id],
            [$address->addressable_type, $address->addressable_id]
        );
    }

    /** @test */
    public function it_belongs_to_cart()
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
    public function it_belongs_to_order()
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
    public function it_belongs_to_shipping()
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
    public function it_has_name_attribute()
    {
        $address = Address::factory()->make();

        $this->assertSame(
            sprintf('%s %s', $address->first_name, $address->last_name),
            $address->name
        );
    }

    /** @test */
    public function it_has_country_name_attribute()
    {
        $address = Address::factory()->make();

        $this->assertSame(
            Countries::name($address->country), $address->countryName
        );
    }

    /** @test */
    public function it_has_alias_attribute()
    {
        $address = Address::factory()->make(['alias' => 'Fake']);

        $this->assertSame('Fake', $address->alias);

        $address->alias = null;
        $address->addressable()->associate($this->user);
        $address->save();

        $this->assertSame("#{$address->id}", $address->alias);
    }

    /** @test */
    public function it_has_custom_attribute()
    {
        $address = Address::factory()->make(['custom' => [
            'key' => 'value',
        ]]);

        $this->assertSame('value', $address->custom('key'));
        $this->assertNull($address->custom('null'));
        $this->assertSame('default', $address->custom('null', 'default'));
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $address = $this->user->addresses()->save(Address::factory()->make());

        $this->assertInstanceOf(Breadcrumbable::class, $address);
        $this->assertSame($address->alias, $address->toBreadcrumb($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $address = Address::factory()->make();

        $address->addressable()->associate($this->user)->save();

        $this->assertSame(
            $address->newQuery()->where('bazar_addresses.alias', 'like', 'test%')->toSql(),
            $address->newQuery()->search('test')->toSql()
        );
    }
}
