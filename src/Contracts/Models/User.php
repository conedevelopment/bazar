<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Breadcrumbable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface User extends Breadcrumbable
{
    /**
     * Get the cart for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne;

    /**
     * Get the orders for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany;

    /**
     * Get the addresses for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany;

    /**
     * Determine if the user is admin.
     *
     * @return bool
     */
    public function isAdmin(): bool;
}
