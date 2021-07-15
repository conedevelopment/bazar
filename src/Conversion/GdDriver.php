<?php

namespace Cone\Bazar\Conversion;

use Cone\Bazar\Models\Medium;
use Cone\Bazar\Support\Facades\Conversion;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GdDriver extends Driver
{
    /**
     * Perform the registered conversions on the medium.
     *
     * @param  \Cone\Bazar\Models\Medium  $medium
     * @return \Cone\Bazar\Models\Medium
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function perform(Medium $medium): Medium
    {
        if (! Storage::disk($medium->disk)->exists($medium->getPath())) {
            throw new FileNotFoundException("The file located at [{$medium->getFullPath()}] is not found.");
        }

        File::ensureDirectoryExists(Storage::disk('local')->path('bazar-tmp'));

        foreach (Conversion::all() as $conversion => $callback) {
            $image = $this->createImage($medium);

            call_user_func_array($callback, [$image]);

            $image->save();

            Storage::disk($medium->disk)->put(
                $medium->getPath($conversion), File::get($image->getPath())
            );

            $image->destroy();
        }

        return $medium;
    }

    /**
     * Create a new image instance.
     *
     * @param  \Cone\Bazar\Models\Medium  $medium
     * @return \Cone\Bazar\Conversion\Image
     */
    protected function createImage(Medium $medium): Image
    {
        $image = new Image($medium);

        return $image->setQuality(
            $this->config['quality'] ?? 70
        );
    }
}
