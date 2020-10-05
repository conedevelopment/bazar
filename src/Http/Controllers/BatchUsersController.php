<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class BatchUsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        if (Gate::getPolicyFor($user = get_class($user))) {
            $this->middleware("can:batchUpdate,{$user}")->only('update');
            $this->middleware("can:batchDelete,{$user}")->only('destroy');
            $this->middleware("can:batchRestore,{$user}")->only('restore');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = Arr::dot($request->except('ids'));

        $data = Collection::make($data)->filter()->mapWithKeys(function ($item, $key) {
            return [str_replace('.', '->', $key) => $item];
        })->all();

        $user->newQuery()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->update($data);

        return Redirect::back()->with(
            'message', __(':count users have been updated.', ['count' => count($ids)])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $users = $user->newQuery()->withTrashed()->whereIn(
            'id', $ids = array_diff($request->input('ids', []), [$request->user()->id])
        );

        $request->has('force') ? $users->forceDelete() : $users->delete();

        return Redirect::back()->with(
            'message', __(':count users have been deleted.', ['count' => count($ids)])
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request, User $user): RedirectResponse
    {
        $user->newQuery()->onlyTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->restore();

        return Redirect::back()->with(
            'message', __(':count users have been restored.', ['count' => count($ids)])
        );
    }
}
