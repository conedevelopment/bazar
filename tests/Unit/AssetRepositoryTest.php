<?php

namespace Bazar\Tests\Unit;

use Bazar\Support\Facades\Asset;
use Bazar\Tests\TestCase;

class AssetRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_register_scripts()
    {
        $this->assertEmpty(Asset::scripts());

        Asset::register('fake-script.js');

        $this->assertSame(['fake-script.js'], Asset::scripts());
    }

    /** @test */
    public function it_can_register_styles()
    {
        $this->assertEmpty(Asset::styles());

        Asset::register('fake-style.css');

        $this->assertSame(['fake-style.css'], Asset::styles());
    }
}
