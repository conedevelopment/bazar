<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Bazar;
use Cone\Bazar\Exceptions\InvalidCurrencyException;
use Cone\Bazar\Tests\TestCase;

class BazarTest extends TestCase
{
    /** @test */
    public function bazar_has_version()
    {
        $this->assertSame(Bazar::VERSION, Bazar::getVersion());
    }

    /** @test */
    public function bazar_has_currencies()
    {
        $this->assertSame(
            $this->app['config']->get('bazar.currencies.available'),
            Bazar::getCurrencies()
        );
    }

    /** @test */
    public function bazar_can_get_currency()
    {
        $this->assertSame($this->app['config']->get('bazar.currencies.default'), Bazar::getCurrency());
    }

    /** @test */
    public function bazar_can_set_currency()
    {
        Bazar::setCurrency('eur');
        $this->assertSame('eur', Bazar::getCurrency());

        $this->expectException(InvalidCurrencyException::class);
        Bazar::setCurrency('fake');
    }
}
