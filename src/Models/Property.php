<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\PropertyFactory;
use Cone\Bazar\Interfaces\Models\Property as Contract;
use Cone\Root\Support\Slug;
use Cone\Root\Traits\InteractsWithProxy;
use Cone\Root\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model implements Contract
{
    use HasFactory;
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
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return PropertyFactory::new();
    }

    /**
     * Get the values for the property.
     */
    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::getProxiedClass());
    }

    /**
     * Get the slug representation of the model.
     */
    public function toSlug(): Slug
    {
        return (new Slug($this))->from('name')->unique();
    }
}
