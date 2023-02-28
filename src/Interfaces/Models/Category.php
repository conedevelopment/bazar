<?php

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Category
{
    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany;
}
