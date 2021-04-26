<?php

namespace Bazar\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

trait BazarRoutable
{
    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if (in_array(SoftDeletes::class, class_uses_recursive(get_called_class()))
            && preg_match('/bazar/', Route::getCurrentRoute()->getName())) {
                return static::proxy()
                            ->newQuery()
                            ->where($field ?: static::proxy()->getRouteKeyName(), $value)
                            ->withTrashed()
                            ->first();
        }

        return parent::resolveRouteBinding($value, $field);
    }
}
