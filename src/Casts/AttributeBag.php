<?php

namespace Cone\Bazar\Casts;

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
     * The default values.
     *
     * @var array
     */
    protected array $defaults = [];

    /**
     * Create a new attribute bag instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        parent::__construct(array_replace_recursive($this->defaults, $items));
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
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class(static::class) implements CastsAttributes
        {
            protected string $class;

            public function __construct(string $class)
            {
                $this->class = $class;
            }

            public function get($model, string $key, $value, array $attributes): AttributeBag
            {
                $class = $this->class;

                return new $class(
                    $value ? json_decode($value, true) : []
                );
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
