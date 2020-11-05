<?php

namespace Bazar\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(static function (Model $model): void {
            $model->slug = $model->slug ?: Str::slug($model->name);
        });
    }
}
