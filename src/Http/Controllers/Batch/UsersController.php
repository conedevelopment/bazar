<?php

namespace Bazar\Http\Controllers\Batch;

use Bazar\Http\Controllers\Controller;
use Bazar\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = User::getProxiedClass())) {
            $this->middleware("can:batchUpdate,{$class}")->only('update');
            $this->middleware("can:batchDelete,{$class}")->only('destroy');
            $this->middleware("can:batchRestore,{$class}")->only('restore');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $data = Arr::dot($request->except('id'));

        $data = Collection::make($data)->filter()->mapWithKeys(static function ($item, string $key): array {
            return [str_replace('.', '->', $key) => $item];
        })->all();

        User::proxy()->newQuery()->whereIn('id', $id = $request->input('id', []))->update($data);

        return Redirect::back()->with(
            'message', __(':count users have been updated.', ['count' => count($id)])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $users = User::proxy()->newQuery()->withTrashed()->whereIn(
            'id', $id = array_diff($request->input('id', []), [$request->user()->id])
        );

        $request->has('force') ? $users->forceDelete() : $users->delete();

        return Redirect::back()->with(
            'message', __(':count users have been deleted.', ['count' => count($id)])
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request): RedirectResponse
    {
        User::proxy()->newQuery()->onlyTrashed()->whereIn('id', $id = $request->input('id', []))->restore();

        return Redirect::back()
                        ->with('message', __(':count users have been restored.', ['count' => count($id)]));
    }
}
