<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Listeners;

use Cone\Bazar\Listeners\ClearCookies;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;

class ClearCookiesTest extends TestCase
{
    protected ClearCookies $listener;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new ClearCookies();
        $this->user = User::factory()->create();
    }

    public function test_listener_clears_cart_cookie_on_logout(): void
    {
        Cookie::queue('cart_id', 'test-cart-id', 864000);

        $this->assertNotEmpty(Cookie::getQueuedCookies());

        $event = new Logout('web', $this->user);

        $this->listener->handle($event);

        $queuedCookies = Cookie::getQueuedCookies();

        $this->assertNotEmpty($queuedCookies);

        $cartCookie = collect($queuedCookies)->firstWhere('name', 'cart_id');

        $this->assertNotNull($cartCookie);
        $this->assertTrue(time() > $cartCookie->getExpiresTime());
    }

    public function test_listener_handles_logout_event(): void
    {
        $event = new Logout('web', $this->user);

        $this->listener->handle($event);

        $this->assertTrue(true);
    }
}
