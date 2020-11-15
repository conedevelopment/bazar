<?php

namespace Bazar\Concerns;

use Bazar\Proxies\Address as AddressProxy;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Addressable
{
    /**
     * Get the address for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function address(): MorphOne
    {
        return $this->morphOne(AddressProxy::getProxiedClass(), 'addressable')->withDefault();
    }
}
