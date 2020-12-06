<?php

namespace Bazar\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Meta
{
    /**
     * Get the parent model for the meta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function parent(): MorphTo;

    /**
     * Get the raw value.
     *
     * @return mixed
     */
    public function getRaw();

    /**
     * Get the value attribute.
     *
     * @param  string|null  $value
     * @return mixed
     */
    public function getValueAttribute($value);

    /**
     * Set the value attribute.
     *
     * @param  mixed  $value
     * @return \Bazar\Contracts\Models\Meta
     */
    public function setValueAttribute($value): Meta;
}
