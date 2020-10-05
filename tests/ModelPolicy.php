<?php

namespace Bazar\Tests;

use Bazar\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can perform any actions.
     *
     * @param  \Bazar\Models\User  $user
     * @param  string  $ability
     * @return mixed
     */
    public function before(User $user, string $ability)
    {
        if ($user && $user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Bazar\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Bazar\Models\User  $user
     * @param  mixed  $model
     * @return mixed
     */
    public function view(User $user, $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Bazar\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Bazar\Models\User  $user
     * @param  mixed  $model
     * @return mixed
     */
    public function update(User $user, $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Bazar\Models\User  $user
     * @param  mixed  $model
     * @return mixed
     */
    public function delete(User $user, $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Bazar\Models\User  $user
     * @param  mixed  $model
     * @return mixed
     */
    public function restore(User $user, $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Bazar\Models\User  $user
     * @param  mixed  $model
     * @return mixed
     */
    public function forceDelete(User $user, $model)
    {
        //
    }

    /**
     * Determine whether the user can batch update the model.
     *
     * @param  \Bazar\Models\User  $user
     * @return mixed
     */
    public function batchUpdate(User $user)
    {
        //
    }

    /**
     * Determine whether the user can batch delete the model.
     *
     * @param  \Bazar\Models\User  $user
     * @return mixed
     */
    public function batchDelete(User $user)
    {
        //
    }

    /**
     * Determine whether the user can batch restore the model.
     *
     * @param  \Bazar\Models\User  $user
     * @return mixed
     */
    public function batchRestore(User $user)
    {
        //
    }
}
