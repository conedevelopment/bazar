<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\MediumFactory;
use Bazar\Jobs\MoveFile;
use Bazar\Jobs\PerformConversions;
use Bazar\Models\Medium;
use Bazar\Tests\TestCase;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class JobTest extends TestCase
{
    /** @test */
    public function a_job_can_move_files()
    {
        $file = UploadedFile::fake()->image('test.png');
        $medium = Medium::createFrom($file->getRealPath());

        $job = new MoveFile($medium, $file->getRealPath());

        $this->assertTrue(is_file($file->getRealPath()));
        $job->handle();
        $this->assertFalse(is_file($file->getRealPath()));

        $this->assertTrue($medium->exists());
        $job->failed(new Exception);
        $this->assertFalse($medium->exists());
    }

    /** @test */
    public function a_job_can_perform_conversions()
    {
        $medium = MediumFactory::new()->create();
        Storage::disk('public')->put(
            $medium->path(),  UploadedFile::fake()->image('test.png')->get()
        );

        $job = new PerformConversions($medium);

        $this->assertFalse(is_file($medium->fullPath('thumb')));
        $job->handle();
        $this->assertTrue(is_file($medium->fullPath('thumb')));

        $this->assertTrue($medium->exists());
        $job->failed(new Exception);
        $this->assertFalse($medium->exists());
        $this->assertFalse(is_file($medium->fullPath('thumb')));
    }
}
