<?php

declare(strict_types=1);

namespace Cone\Bazar\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;

class ClearCookies
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        Cookie::queue(
            Cookie::forget('cart_id')
        );
    }
}
