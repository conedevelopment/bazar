<?php

namespace Bazar\Casts;

use Bazar\Support\Inventory as Handler;
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
     * @return \Bazar\Support\Inventory
     */
    public function get($model, string $key, $value, array $attributes): Handler
    {
        $value = $value ? json_decode($value, true) : [];

        return new Handler($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \Bazar\Support\Inventory|array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value, JSON_NUMERIC_CHECK);
    }
}
