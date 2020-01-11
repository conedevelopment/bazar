<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Medium;
use Bazar\Services\Image;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class ImageTest extends TestCase
{
    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->medium = factory(Medium::class)->create(['file_name' => 'test.jpg']);

        Storage::disk('public')->makeDirectory($this->medium->id);

        $i = imagecreate(800, 400);
        imagejpeg($i, $this->medium->fullPath());
        imagedestroy($i);
    }

    /** @test */
    public function images_can_be_resized()
    {
        Image::make($this->medium)->resize(400)->save('resized');
        [$w, $h] = getimagesize($this->medium->fullPath('resized'));
        $this->assertSame([400, 200], [$w, $h]);

        Image::make($this->medium)->resize(400, 100)->save('resized');
        [$w, $h] = getimagesize($this->medium->fullPath('resized'));
        $this->assertSame([200, 100], [$w, $h]);

        Image::make($this->medium)->width(400)->height(100)->resize()->save('resized');
        [$w, $h] = getimagesize($this->medium->fullPath('resized'));
        $this->assertSame([200, 100], [$w, $h]);
    }

    /** @test */
    public function images_can_be_cropped()
    {
        Image::make($this->medium)->crop(400)->save('cropped');
        [$w, $h] = getimagesize($this->medium->fullPath('cropped'));
        $this->assertSame([400, 400], [$w, $h]);

        Image::make($this->medium)->crop(400, 100)->save('cropped');
        [$w, $h] = getimagesize($this->medium->fullPath('cropped'));
        $this->assertSame([400, 100], [$w, $h]);

        Image::make($this->medium)->width(400)->height(100)->crop()->save('cropped');
        [$w, $h] = getimagesize($this->medium->fullPath('cropped'));
        $this->assertSame([400, 100], [$w, $h]);
    }
}
