<?php

namespace Bazar\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;

class Bag implements CastsInboundAttributes
{
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \Bazar\Support\Bags\Prices  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value, JSON_NUMERIC_CHECK);
    }
}
