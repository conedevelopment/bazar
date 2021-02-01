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

        Asset::script('fake', 'fake-script.js');

        $this->assertSame([['path' => 'fake-script.js', 'type' => 'script']], Asset::scripts());
    }

    /** @test */
    public function it_can_register_styles()
    {
        $this->assertEmpty(Asset::styles());

        Asset::style('fake', 'fake-style.css');

        $this->assertSame([['path' => 'fake-style.css', 'type' => 'style']], Asset::styles());
    }

    /** @test */
    public function it_can_register_icons()
    {
        $this->assertEmpty(Asset::icons());

        Asset::icon('fake', 'fake-icon.svg');

        $this->assertSame([['path' => 'fake-icon.svg', 'type' => 'icon']], Asset::icons());
    }
}
