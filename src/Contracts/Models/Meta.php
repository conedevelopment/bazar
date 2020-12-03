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
     * Set the type attribute.
     *
     * @param  string|null  $value
     * @return \Bazar\Contracts\Models\Meta
     */
    public function setTypeAttribute($value): Meta;

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
