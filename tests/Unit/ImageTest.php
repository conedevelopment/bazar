<?php

namespace Bazar\Tests\Unit;

use Bazar\Conversion\Image;
use Bazar\Database\Factories\MediumFactory;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(Storage::disk('local')->path('bazar-tmp'));
    }

    /** @test */
    public function jpeg_can_be_converted()
    {
        $medium = MediumFactory::new()->create(['file_name' => 'test.jpg']);

        Storage::disk('public')->makeDirectory($medium->id);

        $i = imagecreate(800, 400);
        imagejpeg($i, $medium->fullPath());
        imagedestroy($i);

        ($image = new Image($medium))->quality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->resize()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->crop()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 100], [$w, $h]);
    }

    /** @test */
    public function png_can_be_converted()
    {
        $medium = MediumFactory::new()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->id);

        $i = imagecreate(800, 400);
        imagecolorallocate($i, 0, 0, 0);
        imagesavealpha($i, true);
        imagepng($i, $medium->fullPath());
        imagedestroy($i);

        ($image = new Image($medium))->quality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->resize()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->crop()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 100], [$w, $h]);
    }

    /** @test */
    public function gif_can_be_converted()
    {
        $medium = MediumFactory::new()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->id);

        $i = imagecreate(800, 400);
        imagegif($i, $medium->fullPath());
        imagedestroy($i);

        ($image = new Image($medium))->quality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->resize()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->crop()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 100], [$w, $h]);
    }

    /** @test */
    public function webp_can_be_converted()
    {
        $medium = MediumFactory::new()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->id);

        $i = imagecreatetruecolor(800, 400);
        imagewebp($i, $medium->fullPath());
        imagedestroy($i);

        ($image = new Image($medium))->quality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->resize()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->width(400)->height(100)->crop()->save();
        [$w, $h] = getimagesize($image->path());
        $this->assertSame([400, 100], [$w, $h]);
    }

    /** @test */
    public function not_supported_types_cannot_be_converted()
    {
        $medium = MediumFactory::new()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->id);

        $i = imagecreatetruecolor(800, 400);
        imagexbm($i, $medium->fullPath());
        imagedestroy($i);

        $this->expectExceptionMessage('The file type is not supported');
        ($image = new Image($medium))->quality(70)->resize(400)->save();
    }
}
