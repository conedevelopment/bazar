<?php

namespace Cone\Bazar\Concerns;

use Cone\Bazar\Models\Medium;
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
        return $this->morphToMany(Medium::getProxiedClass(), 'mediable', 'bazar_mediables');
    }
}
