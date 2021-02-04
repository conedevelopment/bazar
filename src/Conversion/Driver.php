<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;
use Bazar\Support\Facades\Conversion;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Perform the registered conversions on the medium.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return \Bazar\Contracts\Models\Medium
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function perform(Medium $medium): Medium
    {
        if (! Storage::disk($medium->disk)->exists($medium->path())) {
            throw new FileNotFoundException("The file located at [{$medium->fullPath()}] is not found.");
        }

        File::ensureDirectoryExists(Storage::disk('local')->path('bazar-tmp'));

        foreach (Conversion::getConversions() as $conversion => $callback) {
            $image = $this->createImage($medium);

            call_user_func_array($callback, [$image, $conversion]);

            $image->save();

            Storage::disk($medium->disk)->put(
                $medium->path($conversion), File::get($image->path())
            );

            $image->destroy();
        }

        return $medium;
    }

    /**
     * Create a new image instance.
     *
     * @param  \Bazar\Contracts\Models\Medium  $meidum
     * @return \Bazar\Conversion\Image
     */
    abstract public function createImage(Medium $medium): Image;
}
