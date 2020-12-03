<?php

namespace Bazar\Models;

use Bazar\Contracts\Models\Meta as Contract;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JsonSerializable;

class Meta extends Model implements Contract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'type',
        'value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_metas';

    /**
     * Get the parent model for the meta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function parent(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Set the type attribute.
     *
     * @param  string|null  $value
     * @return $this
     */
    public function setTypeAttribute($value): Contract
    {
        if (is_null($value)) {
            unset($this->casts['value']);
        } else {
            $this->mergeCasts(['value' => $value]);
        }

        $this->attributes['type'] = $value;

        return $this;
    }

    /**
     * Get the value attribute.
     *
     * @param  string|null  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if ($this->hasCast('value')) {
            return $this->castAttribute('value', $value);
        } elseif (is_numeric($value)) {
            return strpos($value, '.') === false ? (int) $value : (float) $value;
        } elseif (strtotime($value)) {
            return $this->asDateTime($value);
        }

        return json_decode($value, true) ?: $value;
    }

    /**
     * Set the value attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setValueAttribute($value): Contract
    {
        if ($this->isClassCastable('value')) {
            $this->setClassCastableAttribute('value', $value);
        } elseif ($value instanceof DateTimeInterface) {
            $this->attributes['value'] = $this->fromDateTime($value);
        } elseif (is_array($value) || $value instanceof JsonSerializable) {
            $this->attributes['value'] = $this->castAttributeAsJson('value', $value);
        } elseif ($value instanceof Arrayable) {
            $this->attributes['value'] = $this->castAttributeAsJson('value', $value->toArray());
        } elseif ($value instanceof Jsonable) {
            $this->attributes['value'] = $value->toJson();
        } else {
            $this->attributes['value'] = $value;
        }

        return $this;
    }
}
