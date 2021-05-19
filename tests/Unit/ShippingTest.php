<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Taxable;
use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Shipping;
use Bazar\Support\Facades\Shipping as ShippingManager;
use Bazar\Support\Facades\Tax;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class ShippingTest extends TestCase
{
    protected $cart, $shipping;

    public function setUp(): void
    {
        parent::setUp();

        Tax::register('fix-10%', function (Taxable $item) {
            return $item->price * 0.1;
        });

        $this->cart = Cart::factory()->create();
        $this->shipping = Shipping::factory()->make();
        $this->shipping->shippable()->associate($this->cart)->save();
    }

    /** @test */
    public function a_shipping_belongs_to_a_cart()
    {
        $this->assertSame(
            [Cart::class, $this->cart->id],
            [$this->shipping->shippable_type, $this->shipping->shippable_id]
        );
    }

    /** @test */
    public function a_shipping_belongs_to_a_cart_by_default()
    {
        $shipping = new Shipping();

        $this->assertInstanceOf(Cart::class, $shipping->shippable);
    }

    /** @test */
    public function a_shipping_belongs_to_an_order()
    {
        $order = $this->admin->orders()->save(Order::factory()->make());
        $shipping = Shipping::factory()->make();
        $shipping->shippable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$shipping->shippable_type, $shipping->shippable_id]
        );
    }

    /** @test */
    public function a_shipping_has_address()
    {
        $order = $this->admin->orders()->save(Order::factory()->make());
        $shipping = Shipping::factory()->make();
        $shipping->shippable()->associate($order)->save();

        $address = $shipping->address()->save(
            Address::factory()->make()
        );

        $this->assertSame($address->id, $shipping->address->id);
    }

    /** @test */
    public function it_can_calculate_cost()
    {
        $cost = $this->shipping->cost();
        $this->assertSame($cost, $this->shipping->cost);
    }

    /** @test */
    public function it_is_taxable()
    {
        $this->shipping->tax();

        $this->assertInstanceOf(Taxable::class, $this->shipping);
        $this->assertSame($this->shipping->price * 0.1, $this->shipping->tax);
        $this->assertSame(
            Str::currency($this->shipping->tax, $this->shipping->shippable->currency), $this->shipping->formattedTax()
        );
        $this->assertSame($this->shipping->formattedTax(), $this->shipping->formattedTax);
    }

    /** @test */
    public function it_has_price_attribute()
    {
        $this->assertSame($this->shipping->cost, $this->shipping->price);
    }

    /** @test */
    public function it_has_total_attribute()
    {
        $this->assertSame(
            ($this->shipping->price + $this->shipping->tax) * $this->shipping->quantity,
            $this->shipping->total()
        );
        $this->assertSame($this->shipping->total(), $this->shipping->total);
        $this->assertSame(
            Str::currency($this->shipping->total, $this->shipping->shippable->currency),
            $this->shipping->formattedTotal()
        );
        $this->assertSame($this->shipping->formattedTotal(), $this->shipping->formattedTotal);
        $this->assertSame($this->shipping->price * $this->shipping->quantity, $this->shipping->netTotal());
        $this->assertSame($this->shipping->netTotal(), $this->shipping->netTotal);
        $this->assertSame(
            Str::currency($this->shipping->netTotal, $this->shipping->shippable->currency),
            $this->shipping->formattedNetTotal()
        );
        $this->assertSame($this->shipping->formattedNetTotal(), $this->shipping->formattedNetTotal);
    }

    /** @test */
    public function it_has_driver_name()
    {
        $this->assertSame(ShippingManager::driver($this->shipping->driver)->getName(), $this->shipping->driverName);

        $this->shipping->setAttribute('driver', 'fake');
        $this->assertSame('fake', $this->shipping->driverName);
    }
}
