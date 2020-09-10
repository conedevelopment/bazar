<?php

namespace Bazar\Concerns;

use Illuminate\Support\Str;

/**
 * @template ModelT of \Illuminate\Database\Eloquent\Model
 */
trait Sluggable
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(
            /**
             * @param ModelT $model
             */
            function ($model) {
                $model->slug = $model->slug ?: Str::slug($model->name);
            }
        );
    }
}
