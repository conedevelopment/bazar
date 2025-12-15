<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests;

use Cone\Bazar\Bazar;
use Cone\Bazar\Enums\Currency;

class BazarTest extends TestCase
{
    public function test_bazar_has_currencies(): void
    {
        $this->assertSame(
            [Currency::USD, Currency::EUR],
            Bazar::getCurrencies()
        );
    }

    public function test_bazar_can_get_currency(): void
    {
        $this->assertSame(Currency::USD, Bazar::getCurrency());
    }

    public function test_bazar_can_set_currency(): void
    {
        Bazar::setCurrency(Currency::EUR);
        $this->assertSame(Currency::EUR, Bazar::getCurrency());
    }
}
