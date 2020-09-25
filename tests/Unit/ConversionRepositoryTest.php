<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\MediumFactory;
use Bazar\Models\Medium;
use Bazar\Support\Facades\Conversion;
use Bazar\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ConversionRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_register_conversions()
    {
        Conversion::register('4k', function (Medium $medium) {
            //
        });

        $this->assertTrue(Conversion::has('4k'));
    }

    /** @test */
    public function it_can_remove_conversions()
    {
        $this->assertTrue(Conversion::has('thumb'));

        Conversion::remove('thumb');

        $this->assertFalse(Conversion::has('thumb'));
    }

    /** @test */
    public function it_can_perform_conversions()
    {
        $medium = MediumFactory::new()->create([
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
