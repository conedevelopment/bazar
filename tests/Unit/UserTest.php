<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function it_can_have_a_cart()
    {
        $this->assertNull($this->user->cart);

        $cart = $this->user->cart()->save(
            CartFactory::new()->make()
        );

        $this->user->refresh();

        $this->assertSame($cart->id, $this->user->cart->id);
    }

    /** @test */
    public function it_has_orders()
    {
        $orders = $this->user->orders()->saveMany(
            OrderFactory::new()->count(3)->make()
        );

        $this->assertSame(
            $this->user->orders->pluck('id')->all(), $orders->pluck('id')->all()
        );
    }

    /** @test */
    public function it_has_addresses()
    {
        $addresses = $this->user->addresses()->saveMany(
            AddressFactory::new()->count(3)->make()
        );

        $this->assertSame($this->user->addresses->pluck('id')->all(), $addresses->pluck('id')->all());

        $this->assertSame($this->user->address->id, $this->user->addresses->first()->id);

        $this->user->addresses->get(2)->default = true;
        $this->assertSame(
            $this->user->address->id,
            $this->user->addresses->firstWhere('default', true)->id
        );
    }

    /** @test */
    public function it_has_avatar()
    {
        $this->assertEquals(
            asset('vendor/bazar/img/avatar-placeholder.svg'),
            $this->user->avatar
        );
    }

    /** @test */
    public function it_can_be_admin()
    {
        $this->assertFalse($this->user->isAdmin());
        $this->assertTrue($this->admin->isAdmin());
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $this->assertInstanceOf(Breadcrumbable::class, $this->user);
        $this->assertSame($this->user->name, $this->user->toBreadcrumb($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->user->newQuery()->where(function ($q) {
                $q->where('name', 'like', 'test%')
                    ->orWhere('email', 'like', 'test%');
            })->toSql(),
            $this->user->newQuery()->search('test')->toSql()
        );
    }
}
