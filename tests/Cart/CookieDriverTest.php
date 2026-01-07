<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Cart;

use Cone\Bazar\Cart\CookieDriver;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Cookie;

class CookieDriverTest extends TestCase
{
    protected CookieDriver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = new CookieDriver($this->app['config']->get('bazar.cart.drivers.cookie', []));
    }

    public function test_cookie_driver_resolves_cart_from_cookie(): void
    {
        $cart = Cart::factory()->create();

        $this->app['request']->cookies->set('cart_id', $cart->id);

        $resolvedCart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $resolvedCart);
        $this->assertSame($cart->id, $resolvedCart->id);
    }

    public function test_cookie_driver_creates_new_cart_when_no_cookie(): void
    {
        $cart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertTrue($cart->exists);
    }

    public function test_cookie_driver_queues_cookie_after_resolution(): void
    {
        $cart = $this->driver->getModel();

        $queuedCookies = Cookie::getQueuedCookies();

        $this->assertNotEmpty($queuedCookies);
        $this->assertSame('cart_id', $queuedCookies[0]->getName());
        $this->assertSame($cart->getKey(), $queuedCookies[0]->getValue());
    }
}
