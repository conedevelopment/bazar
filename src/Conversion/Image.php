<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Image
{
    /**
     * The medium instance.
     *
     * @var \Bazar\Contracts\Models\Medium
     */
    protected $medium;

    /**
     * The path of the image.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new image instance.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return void
     */
    public function __construct(Medium $medium)
    {
        $this->medium = $medium;

        $this->path = Storage::disk('local')->path('bazar-tmp/'.Str::random(40));
    }

    /**
     * Get the image path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Save the resource.
     *
     * @return void
     */
    abstract public function save(): void;

    /**
     * Destroy the resource.
     *
     * @return void
     */
    abstract public function destroy(): void;
}
