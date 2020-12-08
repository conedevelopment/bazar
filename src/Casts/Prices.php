<?php

namespace Bazar\Casts;

use Bazar\Support\Attributes\Prices as PricesBag;
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
     * @return \Bazar\Support\Attributes\Prices
     */
    public function get($model, string $key, $value, array $attributes): PricesBag
    {
        $value = $value ? json_decode($value, true) : [];

        return new PricesBag($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \Bazar\Support\Attributes\Prices  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value, JSON_NUMERIC_CHECK);
    }
}
