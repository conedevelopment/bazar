<?php

namespace Bazar\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootHasUuid(): void
    {
        static::creating(static function (self $model): void {
            if (! $model->getKey()) {
                $model->setAttribute($model->getKeyName(), (string) Str::uuid());
            }
        });
    }
}
