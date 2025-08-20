<?php

declare(strict_types=1);

namespace Cone\Bazar\Cart;

use Cone\Bazar\Interfaces\Cart\Manager as Contract;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.cart.default');
    }

    /**
     * Create the cookie driver.
     */
    public function createCookieDriver(): CookieDriver
    {
        return new CookieDriver(
            $this->config->get('bazar.cart.drivers.cookie', [])
        );
    }

    /**
     * Create the session driver.
     */
    public function createSessionDriver(): SessionDriver
    {
        return new SessionDriver(
            $this->config->get('bazar.cart.drivers.session', [])
        );
    }
}
