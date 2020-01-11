<?php

namespace Bazar\Concerns;

use Bazar\Models\Medium;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMedia
{
    /**
     * Get all of the media for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(Medium::class, 'mediable');
    }
}
