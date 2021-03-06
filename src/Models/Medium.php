<?php

namespace Bazar\Models;

use Bazar\Concerns\Filterable;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Contracts\Models\Medium as Contract;
use Bazar\Database\Factories\MediumFactory;
use Bazar\Support\Facades\Conversion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Medium extends Model implements Contract
{
    use Filterable;
    use HasFactory;
    use InteractsWithProxy;

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
        'properties' => 'json',
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_media';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(static function (self $medium): void {
            Storage::disk($medium->disk)->deleteDirectory($medium->id);
        });
    }

    /**
     * Get the proxied contract.
     *
     * @return string
     */
    public static function getProxiedContract(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Bazar\Database\Factories\MediumFactory
     */
    protected static function newFactory(): MediumFactory
    {
        return MediumFactory::new();
    }

    /**
     * Create a new medium from the given path.
     *
     * @param  string  $path
     * @return self
     */
    public static function createFrom(string $path): self
    {
        $name = preg_replace('/[\w]{5}__/iu', '', basename($path, '.chunk'));

        if (($type = mime_content_type($path)) !== 'image/svg+xml' && Str::is('image/*', $type)) {
            [$width, $height] = getimagesize($path);
        }

        return static::create([
            'file_name' => $name,
            'mime_type' => $type,
            'width' => isset($width) ? $width : null,
            'height' => isset($height) ? $height : null,
            'disk' => Config::get('bazar.media.disk', 'public'),
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
        $conversions = array_keys(Conversion::all());

        return array_reduce($conversions, function (array $urls, string $name): array {
            return $this->convertable()
                ? array_merge($urls, [$name => $this->getUrl($name)])
                : $urls;
        }, ['original' => $this->getUrl()]);
    }

    /**
     * Determine if the medium should is convertable.
     *
     * @return bool
     */
    public function convertable(): bool
    {
        return $this->isImage && $this->mime_type !== 'image/svg+xml';
    }

    /**
     * Perform the conversions on the medium.
     *
     * @return $this
     */
    public function convert(): self
    {
        return Conversion::perform($this);
    }

    /**
     * Get the path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getPath(?string $conversion = null): string
    {
        $path = "{$this->id}/{$this->file_name}";

        return is_null($conversion)
            ? $path
            : substr_replace(
                $path, "-{$conversion}", -(mb_strlen(Str::afterLast($path, '.')) + 1), -mb_strlen("-{$conversion}")
            );
    }

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getFullPath(?string $conversion = null): string
    {
        if (! in_array($this->disk, ['local', 'public'])) {
            return $this->getUrl($conversion);
        }

        return Storage::disk($this->disk)->path($this->getPath($conversion));
    }

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getUrl(?string $conversion = null): string
    {
        return URL::to(Storage::disk($this->disk)->url($this->getPath($conversion)));
    }

    /**
     * Scope the query only to the given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where($query->qualifyColumn('name'), 'like', "{$value}%");
    }

    /**
     * Scope the query only to the given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(Builder $query, string $value): Builder
    {
        switch ($value) {
            case 'image':
                return $query->where($query->qualifyColumn('mime_type'), 'like', 'image%');
            case 'file':
                return $query->where($query->qualifyColumn('mime_type'), 'not like', 'image%');
            default:
                return $query;
        }
    }
}
