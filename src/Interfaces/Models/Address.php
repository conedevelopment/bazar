<?php

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Breadcrumbable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Address
{
    /**
     * Get the addressable model for the address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable(): MorphTo;

    /**
     * Get a custom property.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function custom(string $key, $default = null);
}
