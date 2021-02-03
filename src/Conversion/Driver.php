<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Driver
{
    /**
     * The medium instance.
     *
     * @var \Bazar\Contracts\Models\Medium
     */
    protected $medium;

    /**
     * The attributes.
     *
     * @var array
     */
    protected $attributes = [
        'width' => 0,
        'height' => 0,
        'quality' => 80,
    ];

    /**
     * Create a new driver instance.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return void
     */
    public function __construct(Medium $medium)
    {
        $this->medium = $medium;

        File::ensureDirectoryExists(Storage::disk('local')->path('bazar-tmp'));
    }

    /**
     * Set the width of the image.
     *
     * @param  int  $width
     * @return $this
     */
    public function width(int $width): self
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    /**
     * Set the height of the image.
     *
     * @param  int  $height
     * @return $this
     */
    public function height(int $height): self
    {
        $this->attributes['height'] = $height;

        return $this;
    }

    /**
     * Set the quality of the image.
     *
     * @param  int  $quality
     * @return $this
     */
    public function quality(int $quality): self
    {
        $this->attributes['quality'] = $quality;

        return $this;
    }

    /**
     * Crop the image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @return $this
     */
    abstract public function crop(int $width = null, int $height = null): self;

    /**
     * Resize the image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @param  bool  $crop
     * @return $this
     */
    abstract public function resize(int $width = null, int $height = null, bool $crop = false): self;

    /**
     * Save the image with the given conversion.
     *
     * @param  string  $conversion
     * @return void
     *
     * @throws \Exception
     */
    public function store(string $conversion): void
    {
        $path = Storage::disk('local')->path('bazar-tmp/'.Str::random(40));

        // $this->saving($path, $conversion);

        // $this->save($path, $conversion);

        Storage::disk($this->medium->disk)->put(
            $this->medium->path($conversion), File::get($path)
        );

        // $this->saved($path, $conversion);
    }

    /**
     * Create the resource.
     *
     * @return $this
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
     protected function create(): self
    {
        if (! Storage::disk($this->medium->disk)->exists($this->medium->path())) {
            throw new FileNotFoundException("The file located at [{$this->medium->fullPath()}] is not found.");
        }

        //

        return $this;
    }
}
