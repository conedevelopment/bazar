<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function a_user_can_have_a_cart()
    {
        $this->assertNull($this->user->cart);

        $cart = $this->user->cart()->save(
            factory(Cart::class)->make()
        );

        $this->user->refresh();

        $this->assertSame($cart->id, $this->user->cart->id);
    }

    /** @test */
    public function a_user_has_orders()
    {
        $orders = $this->user->orders()->saveMany(
            factory(Order::class, 3)->make()
        );

        $this->assertSame(
            $this->user->orders->pluck('id')->all(), $orders->pluck('id')->all()
        );
    }

    /** @test */
    public function a_user_has_addresses()
    {
        $addresses = $this->user->addresses()->saveMany(
            factory(Address::class, 3)->make()
        );

        $this->assertSame(
            $this->user->addresses->pluck('id')->all(), $addresses->pluck('id')->all()
        );
    }

    /** @test */
    public function a_user_has_avatar()
    {
        $this->assertEquals(
            asset('vendor/bazar/img/avatar-placeholder.svg'),
            $this->user->avatar
        );
    }
}
