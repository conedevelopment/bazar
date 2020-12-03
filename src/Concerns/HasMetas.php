<?php

namespace Bazar\Concerns;

use Bazar\Proxies\Meta as MetaProxy;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMetas
{
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
