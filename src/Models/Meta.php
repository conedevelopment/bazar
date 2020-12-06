<?php

namespace Bazar\Models;

use Bazar\Contracts\Models\Meta as Contract;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JsonSerializable;
use Serializable;

class Meta extends Model implements Contract
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => null,
        'value' => null,
    ];

    /**
     * The cached value.
     *
     * @var mixed
     */
    protected $cache = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_metas';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
    * Get the raw value.
     *
     * @return mixed
     */
    public function getRaw()
    {
        return $this->attributes['value'];
    }

    /**
     * Get the value attribute.
     *
     * @param  string|null  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if (! is_null($this->cache)) {
            return $this->cache;
        }

        switch ($this->type) {
            case 'int':
                $this->cache = (int) $value;
                break;
            case 'float':
                $this->cache = $this->fromFloat($value);
                break;
            case 'string':
                $this->cache = (string) $value;
                break;
            case 'bool':
                $this->cache = (bool) $value;
                break;
            case 'object':
                $this->cache = $this->fromJson($value, true);
                break;
            case 'json':
            case 'array':
                $this->cache = $this->fromJson($value);
                break;
            case 'date':
                $this->cache = $this->asDateTime($value);
                break;
            case 'serializable':
                $this->cache = unserialize($value);
                break;
            default:
                $this->cache = $value;
                break;
        }

        return $this->cache;
    }

    /**
     * Set the value attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setValueAttribute($value): Contract
    {
        if ($value instanceof DateTimeInterface) {
            $this->castTo('date', $this->fromDateTime($value));
        } elseif (is_array($value) || $value instanceof JsonSerializable) {
            $this->castTo('array', $this->asJson($value));
        } elseif ($value instanceof Arrayable) {
            $this->castTo('array', $this->asJson($value->toArray()));
        } elseif ($value instanceof Jsonable) {
            $this->castTo('json', $value->toJson());
        } elseif ($value instanceof Serializable) {
            $this->castTo('serializable', serialize($value));
        } elseif (is_object($value)) {
            $this->castTo('object', json_encode($value));
        } elseif (is_int($value) || is_float($value)) {
            $this->castTo(is_int($value) ? 'int' : 'float', $value);
        } elseif (is_bool($value)) {
            $this->castTo('bool', $value ? 1 : 0);
        } elseif (is_string($value)) {
            $this->castTo('string', $value);
        } elseif (is_null($value)) {
            $this->castTo(null, $value);
        }

        $this->cache = null;

        return $this;
    }

    /**
     * Set the type and the converted value.
     *
     * @param  string|null  $type
     * @param  mixed  $value
     * @return void
     */
    protected function castTo(?string $type, $value): void
    {
        $this->attributes['type'] = $type;
        $this->attributes['value'] = $value;
    }
}
