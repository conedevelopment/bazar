<?php

namespace Bazar\Services;

use Bazar\Models\Medium;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image
{
    /**
     * The medium instance.
     *
     * @var \Bazar\Models\Medium
     */
    protected $medium;

    /**
     * The original file type.
     *
     * @var int
     */
    protected $type;

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
     * Create a new image instance.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @return void
     */
    public function __construct(Medium $medium)
    {
        $this->medium = $medium;
        $this->type = exif_imagetype($medium->fullPath());
        $this->source = $this->create();

        File::ensureDirectoryExists(Storage::disk('local')->path('bazar-tmp'));
    }

    /**
     * Make a new image instance.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @return static
     */
    public static function make(Medium $medium): Image
    {
        return new static($medium);
    }

    /**
     * Set the width of the image.
     *
     * @param  int  $width
     * @return $this
     */
    public function width(int $width): Image
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
    public function height(int $height): Image
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
    public function quality(int $quality): Image
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
    public function crop(int $width = null, int $height = null): Image
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
    public function resize(int $width = null, int $height = null, bool $crop = false): Image
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
     * Save the image with the given conversion.
     *
     * @param  string  $conversion
     * @return void
     *
     * @throws \Exception
     */
    public function save(string $conversion): void
    {
        $path = Storage::disk('local')->path('bazar-tmp/'.Str::random(40));

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

        Storage::disk($this->medium->disk)->put(
            $this->medium->path($conversion), File::get($path)
        );

        $this->destroy($path);
    }

    /**
     * Create the resource.
     *
     * @return resource
     *
     * @throws \Exception
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function create() //: resource
    {
        if (! is_file($this->medium->fullPath())) {
            throw new FileNotFoundException("The file located at [{$this->medium->fullPath()}] is not found.");
        }

        switch ($this->type) {
            case IMAGETYPE_GIF:
                return imagecreatefromgif($this->medium->fullPath());
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($this->medium->fullPath());
            case IMAGETYPE_PNG:
                return imagecreatefrompng($this->medium->fullPath());
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($this->medium->fullPath());
            default:
                throw new Exception('The file type is not supported.');
        }
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
