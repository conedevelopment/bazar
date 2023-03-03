<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Interfaces\Models\Property as Contract;
use Cone\Bazar\Resources\PropertyResource;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Support\Slug;
use Cone\Root\Traits\InteractsWithProxy;
use Cone\Root\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model implements Contract, Resourceable
{
    use InteractsWithProxy;
    use Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_properties';

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Get the values for the property.
     */
    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::class);
    }

    /**
     * Get the slug representation of the model.
     */
    public function toSlug(): Slug
    {
        return (new Slug($this))->from('name')->unique();
    }

    /**
     * Get the resource representation of the model.
     */
    public static function toResource(): PropertyResource
    {
        return new PropertyResource(static::class);
    }
}
