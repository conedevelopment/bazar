<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasProperties
{
    /**
     * Get the property values for the model.
     */
    public function propertyValues(): MorphToMany
    {
        return $this->morphToMany(PropertyValue::class, 'buyable', 'bazar_buyable_property_value');
    }

    /**
     * Get the properties for the model.
     */
    public function properties(): HasManyThrough
    {
        return $this->hasManyThrough(Property::class, PropertyValue::class, 'bazar_buyable_property_value.buyable_id', 'id', 'id','property_id')
                    ->join('bazar_buyable_property_value', 'bazar_buyable_property_value.property_value_id', '=', 'bazar_property_values.id')
                    ->where('bazar_buyable_property_value.buyable_type', static::class);
    }
}
