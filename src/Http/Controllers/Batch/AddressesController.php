<?php

namespace Cone\Bazar\Http\Controllers\Batch;

use Cone\Bazar\Http\Controllers\Controller;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class AddressesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Address::getProxiedClass())) {
            $this->middleware("can:batchUpdate,{$class}")->only('update');
            $this->middleware("can:batchDelete,{$class}")->only('destroy');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Bazar\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = Arr::dot($request->except('id'));

        $data = Collection::make($data)->filter()->mapWithKeys(static function ($item, string $key): array {
            return [str_replace('.', '->', $key) => $item];
        })->all();

        $user->addresses()->whereIn('id', $id = $request->input('id', []))->update($data);

        return Redirect::back()
                        ->with('message', __(':count address have been updated.', ['count' => count($id)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Bazar\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $user->addresses()->whereIn('id', $id = $request->input('id', []))->delete();

        return Redirect::back()
                        ->with('message', __(':count addresses have been deleted.', ['count' => count($id)]));
    }
}
