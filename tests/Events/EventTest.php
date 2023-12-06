<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Tests\TestCase;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;

class EventTest extends TestCase
{
    public function test_cookies_are_cleared_after_logout(): void
    {
        Cookie::queue('cart_id', 'fake', 864000);

        $this->assertSame('cart_id', Cookie::getQueuedCookies()[0]->getName());
        $this->assertTrue(time() < Cookie::getQueuedCookies()[0]->getExpiresTime());

        Event::dispatch(new Logout('web', $this->user));

        $this->assertFalse(time() < Cookie::getQueuedCookies()[0]->getExpiresTime());
    }
}
