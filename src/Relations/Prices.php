<?php

declare(strict_types=1);

namespace Cone\Bazar\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Prices extends MorphMany
{
    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints(): void
    {
        parent::addConstraints();

        $this->getQuery()->where($this->getQuery()->qualifyColumn('key'), 'like', 'price_%');
    }
}
