<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Interfaces\Taxable;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Shipping as ShippingManager;
use Cone\Bazar\Support\Facades\Tax;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;

class ShippingTest extends TestCase
{
    protected Cart $cart;

    protected User $user;

    protected Shipping $shipping;

    public function setUp(): void
    {
        parent::setUp();

        Tax::register('fix-10%', function (Taxable $item) {
            return $item->price * 0.1;
        });

        $this->user = User::factory()->create();
        $this->cart = Cart::factory()->create();
        $this->shipping = Shipping::factory()->make();
        $this->shipping->shippable()->associate($this->cart)->save();
    }

    public function test_shipping_belongs_to_a_cart(): void
    {
        $this->assertSame(
            [Cart::class, $this->cart->id],
            [$this->shipping->shippable_type, $this->shipping->shippable_id]
        );
    }

    public function test_shipping_belongs_to_a_cart_by_default(): void
    {
        $shipping = new Shipping();

        $this->assertInstanceOf(Cart::class, $shipping->shippable);
    }

    public function test_shipping_belongs_to_an_order(): void
    {
        $order = $this->user->orders()->save(Order::factory()->make());
        $shipping = Shipping::factory()->make();
        $shipping->shippable()->associate($order)->save();

        $this->assertSame(
            [Order::class, $order->id],
            [$shipping->shippable_type, $shipping->shippable_id]
        );
    }

    public function test_shipping_has_address(): void
    {
        $order = $this->user->orders()->save(Order::factory()->make());
        $shipping = Shipping::factory()->make();
        $shipping->shippable()->associate($order)->save();

        $address = $shipping->address()->save(
            Address::factory()->make()
        );

        $this->assertSame($address->id, $shipping->address->id);
    }

    public function testt_can_calculate_calculateCost(): void
    {
        $cost = $this->shipping->calculateCost();
        $this->assertSame($cost, $this->shipping->cost);
    }

    public function testt_is_taxable(): void
    {
        $this->shipping->calculateTax();

        $this->assertInstanceOf(Taxable::class, $this->shipping);
        $this->assertSame($this->shipping->price * 0.1, $this->shipping->tax);
        $this->assertSame(
            (new Currency($this->shipping->tax, $this->shipping->shippable->currency))->format(),
            $this->shipping->getFormattedTax()
        );
        $this->assertSame($this->shipping->getFormattedTax(), $this->shipping->formattedTax);
    }

    public function testt_has_total_attribute(): void
    {
        $this->assertSame(
            $this->shipping->cost + $this->shipping->tax,
            $this->shipping->getTotal()
        );
        $this->assertSame($this->shipping->getTotal(), $this->shipping->total);
        $this->assertSame(
            (new Currency($this->shipping->total, $this->shipping->shippable->currency))->format(),
            $this->shipping->getFormattedTotal()
        );
        $this->assertSame($this->shipping->getFormattedTotal(), $this->shipping->formattedTotal);
        $this->assertSame($this->shipping->cost, $this->shipping->getSubtotal());
        $this->assertSame($this->shipping->getSubtotal(), $this->shipping->subtotal);
        $this->assertSame(
            (new Currency($this->shipping->subtotal, $this->shipping->shippable->currency))->format(),
            $this->shipping->getFormattedSubtotal()
        );
        $this->assertSame($this->shipping->getFormattedSubtotal(), $this->shipping->formattedSubtotal);
    }

    public function testt_has_driver_name(): void
    {
        $this->assertSame(ShippingManager::driver($this->shipping->driver)->getName(), $this->shipping->driverName);

        $this->shipping->setAttribute('driver', 'fake');
        $this->assertSame('fake', $this->shipping->driverName);
    }
}
