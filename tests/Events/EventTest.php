<?php

namespace Cone\Bazar\Tests\Events;

use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;

class EventTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_cookies_are_cleared_after_logout(): void
    {
        Cookie::queue('cart_id', 'fake', 864000);

        $this->assertSame('cart_id', Cookie::getQueuedCookies()[0]->getName());
        $this->assertTrue(time() < Cookie::getQueuedCookies()[0]->getExpiresTime());

        Event::dispatch(new Logout('web', $this->user));

        $this->assertFalse(time() < Cookie::getQueuedCookies()[0]->getExpiresTime());
    }
}
