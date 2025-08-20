<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Category
{
    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany;
}
