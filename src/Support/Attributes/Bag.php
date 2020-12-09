<?php

namespace Bazar\Support\Attributes;

use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

abstract class Bag implements Arrayable, ArrayAccess, IteratorAggregate, Jsonable, JsonSerializable, Stringable
{
    /**
     * The bag items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new bag instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = array_replace($this->items, $items);
    }

    /**
     * Set the value of the given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function set(string $key, $value): Bag
    {
        $this->offsetSet($key, $value);

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
        return $this->offsetGet($key) ?: $default;
    }

    /**
     * Remove the given key from the items.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key): void
    {
        $this->offsetUnset($key);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function ($item) {
            return $item instanceof Arrayable ? $item->toArray() : $item;
        }, $this->items);
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
     * Determine if the offset exists in the items.
     *
     * @param  string|int  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get the value of the given offset.
     *
     * @param  string|int  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Get the value of the given offset.
     *
     * @param  string|int|null  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the value of the given offset.
     *
     * @param  string|int  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    /**
     * Get the iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Convert the object to its string representation.
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
}
