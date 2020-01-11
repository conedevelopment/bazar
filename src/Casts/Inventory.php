<?php

namespace Bazar\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Inventory implements CastsAttributes
{
    /**
     * The default value.
     *
     * @var array
     */
    protected $default = [
        'files' => [],
        'sku' => null,
        'weight' => null,
        'quantity' => null,
        'virtual' => false,
        'downloadable' => false,
        'dimensions' => ['length' => null, 'width' => null, 'height' => null],
    ];

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

        return array_replace_recursive($this->default, $value);
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
