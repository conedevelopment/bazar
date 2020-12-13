<?php

namespace Bazar\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootSluggable(): void
    {
        static::saving(static function (self $model): void {
            $model->slug = $model->slug ?: Str::slug($model->name);
        });
    }
}
