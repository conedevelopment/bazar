<?php

namespace Bazar\Cart;

use Bazar\Contracts\Cart\Manager as Contract;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('bazar.cart.default');
    }

    /**
     * Create the cookie driver.
     *
     * @return \Bazar\Cart\CookieDriver
     */
    public function createCookieDriver(): CookieDriver
    {
        return new CookieDriver(
            $this->config->get('bazar.cart.drivers.cookie', [])
        );
    }

    /**
     * Create the session driver.
     *
     * @return \Bazar\Cart\SessionDriver
     */
    public function createSessionDriver(): SessionDriver
    {
        return new SessionDriver(
            $this->config->get('bazar.cart.drivers.session', [])
        );
    }
}
