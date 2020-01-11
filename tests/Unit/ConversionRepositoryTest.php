<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Medium;
use Bazar\Support\Facades\Conversion;
use Bazar\Tests\TestCase;

class ConversionRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_register_or_remove_conversions()
    {
        Conversion::register('4k', function (Medium $medium) {
            //
        });

        $this->assertTrue(Conversion::has('4k'));

        Conversion::remove('4k');

        $this->assertFalse(Conversion::has('4k'));
    }
}
