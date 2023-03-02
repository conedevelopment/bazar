<?php

namespace Cone\Bazar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_property_values';

    /**
     * Get the property for the property value.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
