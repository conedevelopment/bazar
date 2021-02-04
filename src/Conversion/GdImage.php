<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;
use Exception;

class GdImage extends Image
{
    /**
     * The file type.
     *
     * @var int
     */
    protected $type;

    /**
     * The resource.
     *
     * @var resource
     */
    protected $resource;

    /**
     * The attributes.
     *
     * @var array
     */
    protected $attributes = [
        'width' => 0,
        'height' => 0,
        'quality' => 70,
    ];

    /**
     * Create a new GD image instance.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return void
     */
    public function __construct(Medium $medium)
    {
        parent::__construct($medium);

        $this->type = exif_imagetype($medium->fullPath());

        $this->create();
    }

    /**
     * Set the width of the image.
     *
     * @param  int  $width
     * @return $this
     */
    public function width(int $width): GdImage
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
    public function height(int $height): GdImage
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
    public function quality(int $quality): GdImage
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
    public function crop(int $width = null, int $height = null): GdImage
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
    public function resize(int $width = null, int $height = null, bool $crop = false): GdImage
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

        $resource = imagecreatetruecolor($width, $height);

        if (in_array($this->type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            imagealphablending($resource, false);
            imagesavealpha($resource, true);
            imagefill($resource, 0, 0, imagecolorallocatealpha($resource, 0, 0, 0, 127));
        }

        imagecopyresampled($resource, $this->resource, 0, 0, $x, $y, $width, $height, $originalWidth, $originalHeight);

        imagedestroy($this->resource);
        $this->resource = $resource;
        unset($resource);

        return $this;
    }

    /**
     * Save the resouce.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save(): void
    {
        switch ($this->type) {
            case IMAGETYPE_GIF:
                imagegif($this->resource, $this->path);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($this->resource, $this->path, $this->attributes['quality']);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->resource, $this->path, 1);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($this->resource, $this->path, $this->attributes['quality']);
                break;
            default:
                throw new Exception('The file type is not supported.');
        }
    }

    /**
     * Create the resource.
     *
     * @return void
     *
     * @throws \Exception
     */
     protected function create(): void
    {
        switch ($this->type) {
            case IMAGETYPE_GIF:
                $this->resource = imagecreatefromgif($this->medium->fullPath());
                break;
            case IMAGETYPE_JPEG:
                $this->resource = imagecreatefromjpeg($this->medium->fullPath());
                break;
            case IMAGETYPE_PNG:
                $this->resource = imagecreatefrompng($this->medium->fullPath());
                break;
            case IMAGETYPE_WEBP:
                $this->resource = imagecreatefromwebp($this->medium->fullPath());
                break;
            default:
                throw new Exception('The file type is not supported.');
        }
    }

    /**
     * Destroy the resource.
     *
     * @return void
     */
    public function destroy(): void
    {
        unlink($this->path);
        imagedestroy($this->resource);
    }
}
