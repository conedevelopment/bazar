<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Address
{
    /**
     * Get the addressable model for the address.
     */
    public function addressable(): MorphTo;
}
