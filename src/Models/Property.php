<?php

namespace Cone\Bazar\Models;

use Cone\Root\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
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
     * Get the values for the property.
     */
    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::class);
    }
}
