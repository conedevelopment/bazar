<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function a_user_can_have_a_cart()
    {
        $this->assertNull($this->user->cart);

        $cart = $this->user->cart()->save(
            CartFactory::new()->make()
        );

        $this->user->refresh();

        $this->assertSame($cart->id, $this->user->cart->id);
    }

    /** @test */
    public function a_user_has_orders()
    {
        $orders = $this->user->orders()->saveMany(
            OrderFactory::new()->count(3)->make()
        );

        $this->assertSame(
            $this->user->orders->pluck('id')->all(), $orders->pluck('id')->all()
        );
    }

    /** @test */
    public function a_user_has_addresses()
    {
        $addresses = $this->user->addresses()->saveMany(
            AddressFactory::new()->count(3)->make()
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
