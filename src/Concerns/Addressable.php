<?php

namespace Bazar\Concerns;

use Bazar\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Addressable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootAddressable(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model)) || $model->forceDeleting) {
                $model->address()->delete();
            }
        });
    }

    /**
     * Get the address for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::getProxiedClass(), 'addressable')->withDefault();
    }
}
