<?php

namespace Cone\Bazar\Tests;

use Cone\Bazar\Bazar;
use Cone\Bazar\Exceptions\InvalidCurrencyException;

class BazarTest extends TestCase
{
    public function test_bazar_has_currencies(): void
    {
        $this->assertSame(
            ['USD', 'EUR'],
            Bazar::getCurrencies()
        );
    }

    public function test_bazar_can_get_currency(): void
    {
        $this->assertSame('USD', Bazar::getCurrency());
    }

    public function test_bazar_can_set_currency(): void
    {
        Bazar::setCurrency('EUR');
        $this->assertSame('EUR', Bazar::getCurrency());

        $this->expectException(InvalidCurrencyException::class);
        Bazar::setCurrency('fake');
    }
}
