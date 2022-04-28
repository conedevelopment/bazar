<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Address
{
    /**
     * Get the addressable model for the address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable(): MorphTo;
}
