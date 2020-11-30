<?php

namespace Bazar\Support;

use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use IteratorAggregate;
use Stringable;

class Inventory implements Arrayable, ArrayAccess, IteratorAggregate, Jsonable, Stringable
{
    /**
     * The inventory values.
     *
     * @var array
     */
    protected $values = [
        'files' => [],
        'sku' => null,
        'weight' => null,
        'quantity' => null,
        'virtual' => false,
        'downloadable' => false,
        'dimensions' => ['length' => null, 'width' => null, 'height' => null],
    ];

    /**
     * Create a new inventory instance.
     *
     * @param  array  $values
     * @return void
     */
    public function __construct(array $values = [])
    {
        $this->values = array_replace_recursive($this->values, $values);
    }

    /**
     * Get the formatted dimensions.
     *
     * @param  string  $glue
     * @return string|null
     */
    public function formattedDimensions(string $glue = 'x'): ?string
    {
        if (! $dimensions = array_filter((array) $this->get('dimensions'))) {
            return null;
        }

        return sprintf('%s %s', implode($glue, $dimensions), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
     *
     * @return string|null
     */
    public function formattedWeight(): ?string
    {
        if (! $weight = $this->get('weight')) {
            return null;
        }

        return sprintf('%s %s', $weight, Config::get('bazar.weight_unit'));
    }

    /**
     * Determine if the stockable model is virtual.
     *
     * @return bool
     */
    public function virtual(): bool
    {
        return (bool) $this->get('virtual', false);
    }

    /**
     * Determine if the stockable model is downloadable.
     *
     * @return bool
     */
    public function downloadable(): bool
    {
        return (bool) $this->get('downloadable', false);
    }

    /**
     * Determine if the stockable model tracks quantity.
     *
     * @return bool
     */
    public function tracksQuantity(): bool
    {
        return ! is_null($this->get('quantity'));
    }

    /**
     * Determine if the stockable model is available.
     *
     * @param  float  $quantity
     * @return bool
     */
    public function available(float $quantity = 1): bool
    {
        $stock = $this->get('quantity', 0);

        return ! $this->tracksQuantity() || (min($stock, $quantity) > 0 && $stock >= $quantity);
    }

    /**
     * Increment the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function incrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $this->set('quantity', $this->get('quantity', 0) + $quantity);
        }
    }

    /**
     * Decrement the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function decrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $this->set('quantity', max($this->get('quantity', 0) - $quantity, 0));
        }
    }

    /**
     * Set the value of the given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function set(string $key, $value): Inventory
    {
        Arr::set($this->values, $key, $value);

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
        return Arr::get($this->values, $key, $default);
    }

    /**
     * Remove the given key from the values.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key): void
    {
        Arr::forget($this->values, $key);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
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
     * Determine if the offset exists in the values.
     *
     * @param  string|int  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->values[$key]);
    }

    /**
     * Get the value of the given offset.
     *
     * @param  string|int  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->values[$key] ?? null;
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
            $this->values[] = $value;
        } else {
            $this->values[$key] = $value;
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
        unset($this->values[$key]);
    }

    /**
     * Get the iterator for the values.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    /**
     * Convert the object to its string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson(JSON_NUMERIC_CHECK);
    }

    /**
     * Dynamically get the given property.
     *
     * @param  string  $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->values[$property] ?? null;
    }

    /**
     * Dynamically set the given property value.
     *
     * @param  string  $property
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $property, $value): void
    {
        $this->offsetSet($property, $value);
    }

    /**
     * Determine if an offset exists on the inventory.
     *
     * @param  string|int  $key
     * @return bool
     */
    public function __isset($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an value on the inventory.
     *
     * @param  string|int  $key
     * @return void
     */
    public function __unset($key): void
    {
        $this->offsetUnset($key);
    }
}
