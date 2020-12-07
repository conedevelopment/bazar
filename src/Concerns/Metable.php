<?php

namespace Bazar\Concerns;

use Bazar\Proxies\Meta as MetaProxy;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Metable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootMetable(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses($model)) || $model->forceDeleting) {
                $model->metas()->delete();
            }
        });
    }

    /**
     * Get the metas for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function metas(): MorphMany
    {
        return $this->morphMany(MetaProxy::getProxiedClass(), 'parent');
    }
}
