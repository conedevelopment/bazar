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
        if (in_array(SoftDeletes::class, class_uses($this)) && preg_match('/bazar/', Route::getCurrentRoute()->getName())) {
            return $this->where(
                $field ?: $this->getRouteKeyName(), $value
            )->withTrashed()->firstOrFail();
        }

        return parent::resolveRouteBinding($value, $field);
    }
}
