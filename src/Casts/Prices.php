<?php

namespace Bazar\Casts;

use Bazar\Bazar;
use Bazar\Support\Facades\Currency;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Prices implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, string $key, $value, array $attributes): array
    {
        $value = $value ? json_decode($value, true) : [];

        return array_replace_recursive(array_fill_keys(
            array_keys(Bazar::currencies()), ['normal' => null, 'sale' => null]
        ), $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value);
    }
}
