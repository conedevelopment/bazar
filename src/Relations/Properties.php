<?php

namespace Cone\Bazar\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Properties extends MorphMany
{
    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints(): void
    {
        parent::addConstraints();

        $this->query->where($this->query->qualifyColumn('key'), 'like', 'property_%');
    }
}
