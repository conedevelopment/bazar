<?php

namespace Bazar\Concerns;

use Bazar\Proxies\Medium as MediumProxy;
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
        return $this->morphToMany(MediumProxy::getProxiedClass(), 'mediable', 'bazar_mediables');
    }
}
