<?php

namespace Bazar\Support;

use ArrayObject;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use JsonSerializable;
use Stringable;

abstract class AttributeBag extends ArrayObject implements Arrayable, Castable, Jsonable, JsonSerializable, Stringable
{
    /**
     * The bag items.
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * Create a new bag instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        parent::__construct(array_replace($this->defaults, $items));
    }

    /**
     * Set the value of the given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function set(string $key, $value): AttributeBag
    {
        $items = $this->toArray();

        Arr::set($items, $key, $value);

        $this->exchangeArray($items);

        return $this;
    }

    /**
     * Get the value of the given key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->toArray(), $key, $default);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Prepare the object for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Dynamically get the given property.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Dynamically set the given property value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Determine if an offset exists on the items.
     *
     * @param  string|int  $key
     * @return bool
     */
    public function __isset($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an value on the items.
     *
     * @param  string|int  $key
     * @return void
     */
    public function __unset($key): void
    {
        $this->offsetUnset($key);
    }

    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return object
     */
    public static function castUsing(array $arguments): object
    {
        return new class(static::class) implements CastsAttributes
        {
            protected $class;

            public function __construct(string $class)
            {
                $this->class = $class;
            }

            public function get($model, string $key, $value, array $attributes): AttributeBag
            {
                $class = $this->class;

                $value = $value ? json_decode($value, true) : [];

                return new $class($value);
            }

            public function set($model, string $key, $value, array $attributes): string
            {
                return json_encode($value, JSON_NUMERIC_CHECK);
            }

            public function serialize($model, string $key, $value, array $attributes): array
            {
                return $value->getArrayCopy();
            }
        };
    }
}
