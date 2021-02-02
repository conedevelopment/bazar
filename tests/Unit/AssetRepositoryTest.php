<?php

namespace Bazar\Tests\Unit;

use Bazar\Support\Facades\Asset;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AssetRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_register_scripts()
    {
        $this->assertEmpty(Asset::scripts());

        Asset::script('fake', 'fake-script.js');

        $this->assertEquals([[
            'source' => 'fake-script.js',
            'type' => 'script',
            'target' => public_path('vendor/fake/fake-script.js'),
            'url' => URL::asset('vendor/fake/fake-script.js'),
        ]], Asset::scripts());
    }

    /** @test */
    public function it_can_register_styles()
    {
        $this->assertEmpty(Asset::styles());

        Asset::style('fake', 'fake-style.css');

        $this->assertEquals([[
            'source' => 'fake-style.css',
            'type' => 'style',
            'target' => public_path('vendor/fake/fake-style.css'),
            'url' => URL::asset('vendor/fake/fake-style.css'),
        ]], Asset::styles());
    }

    /** @test */
    public function it_can_register_icons()
    {
        $this->assertEmpty(Asset::icons());

        Asset::icon('fake', 'fake-icon.svg');

        $this->assertSame([['source' => 'fake-icon.svg', 'type' => 'icon']], Asset::icons());
    }

    /** @test */
    public function it_can_link_assets()
    {
        Storage::fake('local');

        Storage::disk('local')->put('asset-repostory/fake-script.js', '');
        Storage::disk('local')->put('asset-repostory/fake-style.css', '');

        Asset::script('fake', Storage::disk('local')->path('asset-repostory/fake-script.js'));
        Asset::style('fake', Storage::disk('local')->path('asset-repostory/fake-style.css'));
        Asset::icon('fake', 'fake-icon.svg');

        Asset::link();

        $this->assertTrue(is_link(public_path('vendor/fake/fake-script.js')));
        $this->assertTrue(is_link(public_path('vendor/fake/fake-style.css')));
    }
}
