<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Bazar;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class SupportTest extends TestCase
{
    /** @test */
    public function str_has_currency_macro()
    {
        Bazar::setCurrency('eur');
        $this->assertEquals('1,300 EUR', Str::currency(1300));

        Bazar::setCurrency('usd');
        $this->assertEquals('150,300,400 USD', Str::currency(150300400));

        $this->assertEquals('150,300,400 HUF', Str::currency(150300400, 'huf'));
    }
}
