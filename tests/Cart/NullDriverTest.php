<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Cart;

use Cone\Bazar\Cart\NullDriver;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;

class NullDriverTest extends TestCase
{
    protected NullDriver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = new NullDriver([]);
    }

    public function test_null_driver_resolves_new_cart_instance(): void
    {
        $cart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertFalse($cart->exists);
    }

    public function test_null_driver_associates_cart_with_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $cart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertSame($user->id, $cart->user_id);
    }

    public function test_null_driver_creates_cart_without_user_when_not_authenticated(): void
    {
        $cart = $this->driver->getModel();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertNull($cart->user_id);
    }
}
