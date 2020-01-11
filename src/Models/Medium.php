<?php

namespace Bazar\Models;

use Bazar\Support\Facades\Conversion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Medium extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'urls',
        'is_image',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'properties' => '{"alt": null}',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'disk',
        'name',
        'size',
        'width',
        'height',
        'file_name',
        'mime_type',
        'properties',
    ];

    /**
     * Create a new medium from the given path.
     *
     * @param  string  $path
     * @return static
     */
    public static function createFrom(string $path): Medium
    {
        $name = preg_replace('/[\w]{5}__/iu', '', basename($path, '.part'));

        if (Str::is('image/*', $type = mime_content_type($path))) {
            [$width, $height] = getimagesize($path);
        }

        return static::create([
            'file_name' => $name,
            'mime_type' => $type,
            'width' => $width ?? null,
            'height' => $height ?? null,
            'disk' => config('bazar.media.disk'),
            'size' => round(filesize($path) / 1024),
            'name' => pathinfo($name, PATHINFO_FILENAME),
        ]);
    }

    /**
     * Determine if the file is image.
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool
    {
        return Str::is('image/*', $this->mime_type);
    }

    /**
     * Get the conversion URLs.
     *
     * @return array
     */
    public function getUrlsAttribute(): array
    {
        return Conversion::keys()->reduce(function ($urls, $name) {
            return $this->isImage ? array_merge($urls, [$name => $this->url($name)]) : $urls;
        }, ['full' => $this->url()]);
    }

    /**
     * Perform the conversions on the medium.
     *
     * @return $this
     */
    public function convert(): Medium
    {
        return Conversion::perform($this);
    }

    /**
     * Get the path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function path(string $conversion = null): string
    {
        $path = "{$this->id}/{$this->file_name}";

        return is_null($conversion) ? $path : substr_replace(
            $path, "-{$conversion}", -(mb_strlen(Str::afterLast($path, '.')) + 1), -mb_strlen("-{$conversion}")
        );
    }

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function fullPath(string $conversion = null): string
    {
        if (! in_array($this->disk, ['local', 'public'])) {
            return $this->url($conversion);
        }

        return Storage::disk($this->disk)->path($this->path($conversion));
    }

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function url(string $conversion = null): string
    {
        return url(Storage::disk($this->disk)->url($this->path($conversion)));
    }
}
