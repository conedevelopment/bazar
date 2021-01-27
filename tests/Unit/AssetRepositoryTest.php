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

        Asset::script('fake', 'fake-path');

        $this->assertSame(['fake-script' => 'fake-path'], Asset::scripts());
    }

    /** @test */
    public function it_can_register_styles()
    {
        $this->assertEmpty(Asset::styles());

        Asset::style('fake', 'fake-path');

        $this->assertSame(['fake-style' => 'fake-path'], Asset::styles());
    }
}
