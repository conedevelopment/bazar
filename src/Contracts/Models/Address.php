<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Breadcrumbable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Address extends Breadcrumbable
{
    /**
     * Get the addressable model for the address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable(): MorphTo;

    /**
     * Get the alias attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getAliasAttribute(?string $value = null): ?string;

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getNameAttribute(): string;

    /**
     * Get the country name attribute.
     *
     * @return string|null
     */
    public function getCountryNameAttribute(): ?string;

    /**
     * Get a custom property.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function custom(string $key, $default = null);
}
