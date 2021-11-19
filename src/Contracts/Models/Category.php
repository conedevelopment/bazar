<?php

namespace Cone\Bazar\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Category
{
    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany;
}
