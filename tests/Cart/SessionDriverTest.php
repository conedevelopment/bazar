<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Cart;

use Cone\Bazar\Cart\SessionDriver;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Tests\TestCase;

class SessionDriverTest extends TestCase
{
    protected SessionDriver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = new SessionDriver($this->app['config']->get('bazar.cart.drivers.session', []));
    }

    public function test_session_driver_resolves_cart_from_session(): void
    {
        $cart = Cart::factory()->create();

        $this->app['request']->session()->put('cart_id', $cart->id);

        $resolvedCart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $resolvedCart);
        $this->assertSame($cart->id, $resolvedCart->id);
    }

    public function test_session_driver_creates_new_cart_when_no_session(): void
    {
        $cart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertTrue($cart->exists);
    }

    public function test_session_driver_stores_cart_id_in_session_after_resolution(): void
    {
        $cart = $this->driver->getModel();

        $this->assertSame($cart->getKey(), $this->app['request']->session()->get('cart_id'));
    }
}
