<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;
use Exception;
use Illuminate\Support\Facades\File;

class GdDriver extends Driver
{
    /**
     * The source.
     *
     * @var resource
     */
    protected $source;

    /**
     * The target.
     *
     * @var resource
     */
    protected $target;

    /**
     * The original file type.
     *
     * @var int
     */
    protected $type;

    /**
     * Create a new GD driver instance.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return void
     */
    public function __construct(Medium $medium)
    {
        parent::__construct($medium);

        $this->type = exif_imagetype($medium->fullPath());
    }

    public function source()
    {
        switch ($this->type) {
            case IMAGETYPE_GIF:
                $this->source = imagecreatefromgif($this->medium->fullPath());
            case IMAGETYPE_JPEG:
                $this->source = imagecreatefromjpeg($this->medium->fullPath());
            case IMAGETYPE_PNG:
                $this->source = imagecreatefrompng($this->medium->fullPath());
            case IMAGETYPE_WEBP:
                $this->source = imagecreatefromwebp($this->medium->fullPath());
            default:
                throw new Exception('The file type is not supported.');
        }
    }

    /**
     * Save the modified image to the given path.
     *
     * @param  string  $path
     * @param  string  $conversion
     * @return void
     */
    public function save(string $path, string $conversion): void
    {
        switch ($this->type) {
            case IMAGETYPE_GIF:
                imagegif($this->target, $path);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($this->target, $path, $this->attributes['quality']);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->target, $path, (int) min($this->attributes['quality'] / 100, 9));
                break;
            case IMAGETYPE_WEBP:
                imagewebp($this->target, $path, $this->attributes['quality']);
                break;
            default:
                throw new Exception('The file type is not supported.');
        }
    }

    /**
     * Crop the image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @return $this
     */
    public function crop(int $width = null, int $height = null): self
    {
        $this->resize($width, $height, true);

        return $this;
    }

    /**
     * Resize the image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @param  bool  $crop
     * @return $this
     */
    public function resize(int $width = null, int $height = null, bool $crop = false): self
    {
        $x = $y = 0;
        [$originalWidth, $originalHeight] = getimagesize($this->medium->fullPath());

        $width = $width ?: $this->attributes['width'];
        $width = $width ? min($width, $originalWidth) : $originalWidth;

        $height = $height ?: $this->attributes['height'];
        $height = $height ? min($height, $originalHeight) : ($crop ? $width : $originalHeight);

        if (! $crop && $width <= $height) {
            $height = ($width / $originalWidth) * $originalHeight;
        } elseif (! $crop && $height < $width) {
            $width = ($height / $originalHeight) * $originalWidth;
        } elseif ($crop && $originalWidth < $originalHeight) {
            $y = ($originalHeight / 2) - ($originalWidth / 2);
            $originalHeight = $originalWidth;
        } elseif ($crop && $originalHeight < $originalWidth) {
            $x = ($originalWidth / 2) - ($originalHeight / 2);
            $originalWidth = $originalHeight;
        }

        $this->target = imagecreatetruecolor($width, $height);

        if (in_array($this->type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            imagealphablending($this->target, false);
            imagesavealpha($this->target, true);
            imagefill($this->target, 0, 0, imagecolorallocatealpha($this->target, 0, 0, 0, 127));
        }

        imagecopyresampled(
            $this->target, $this->source, 0, 0, $x, $y, $width, $height, $originalWidth, $originalHeight
        );

        return $this;
    }

    /**
     * Destroy the resource.
     *
     * @param  string  $path
     * @return void
     */
    protected function destroy(string $path): void
    {
        File::delete($path);
        imagedestroy($this->source);
        imagedestroy($this->target);
    }
}
