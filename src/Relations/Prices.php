<?php

namespace Cone\Bazar\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Prices extends MorphMany
{
    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints(): void
    {
        parent::addConstraints();

        $this->query->where($this->query->qualifyColumn('key'), 'like', 'price_%');
    }
}
