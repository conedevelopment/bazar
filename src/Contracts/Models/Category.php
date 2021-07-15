<?php

namespace Cone\Bazar\Contracts\Models;

use Cone\Bazar\Contracts\Breadcrumbable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Category extends Breadcrumbable
{
    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany;
}
