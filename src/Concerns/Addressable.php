<?php

namespace Bazar\Concerns;

use Bazar\Models\Address;
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
        return $this->morphOne(Address::class, 'addressable')->withDefault();
    }
}
