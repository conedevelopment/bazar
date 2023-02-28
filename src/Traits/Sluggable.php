<?php

namespace Cone\Bazar\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the trait.
     */
    protected static function bootSluggable(): void
    {
        static::saving(static function (self $model): void {
            $model->slug = $model->slug ?: Str::slug($model->name);
        });
    }
}
