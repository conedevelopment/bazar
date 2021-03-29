<?php

namespace Bazar\Tests\Feature;

use Bazar\Contracts\Conversion\Manager;
use Bazar\Conversion\GdDriver;
use Bazar\Models\Medium;
use Bazar\Support\Facades\Conversion;
use Bazar\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ConversionDriverTest extends TestCase
{
    protected $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->app->make(Manager::class);
    }

    /** @test */
    public function it_has_gd_driver()
    {
        $this->assertInstanceOf(GdDriver::class, $this->manager->driver('gd'));
    }

    /** @test */
    public function it_can_register_conversions()
    {
        Conversion::registerConversion('4k', function (Medium $medium) {
            //
        });

        $this->assertTrue(array_key_exists('4k', Conversion::getConversions()));
    }

    /** @test */
    public function it_can_remove_conversions()
    {
        $this->assertTrue(array_key_exists('thumb', Conversion::getConversions()));

        Conversion::removeConversion('thumb');

        $this->assertFalse(array_key_exists('thumb', Conversion::getConversions()));
    }

    /** @test */
    public function it_can_perform_conversions()
    {
        $medium = Medium::factory()->create([
            'name' => 'test',
            'file_name' => 'test.png',
            'mime_type' => 'image/png',
        ]);

        $image = UploadedFile::fake()->image('test.png');
        Storage::disk('public')->put(
            "{$medium->id}/test.png", File::get($image->getRealPath())
        );

        Conversion::perform($medium);

        Storage::disk($medium->disk)->assertExists($medium->path());
        Storage::disk($medium->disk)->assertExists($medium->path('medium'));
        Storage::disk($medium->disk)->assertExists($medium->path('thumb'));

        $medium->delete();

        Storage::disk($medium->disk)->assertMissing($medium->id);
    }
}
