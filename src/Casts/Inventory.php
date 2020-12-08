<?php

namespace Bazar\Casts;

use Bazar\Support\Attributes\Inventory as InventoryBag;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Inventory implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Bazar\Support\Attributes\Inventory
     */
    public function get($model, string $key, $value, array $attributes): InventoryBag
    {
        $value = $value ? json_decode($value, true) : [];

        return new InventoryBag($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \Bazar\Support\Attributes\Inventory|array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value, JSON_NUMERIC_CHECK);
    }
}
