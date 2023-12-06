<?php

namespace Cone\Bazar\Tests\Support;

use Cone\Bazar\Bazar;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class StrTest extends TestCase
{
    public function test_str_has_currency_macro(): void
    {
        Bazar::setCurrency('eur');
        $this->assertEquals('1,300 EUR', Str::currency(1300));

        Bazar::setCurrency('usd');
        $this->assertEquals('150,300,400 USD', Str::currency(150300400));

        $this->assertEquals('150,300,400 HUF', Str::currency(150300400, 'huf'));
    }
}
