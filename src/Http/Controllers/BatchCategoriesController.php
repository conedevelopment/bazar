<?php

namespace Bazar\Http\Controllers;

use Bazar\Proxies\Category as CategoryProxy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class BatchCategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = CategoryProxy::getProxiedClass())) {
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

        CategoryProxy::query()->whereIn(
            'id', $id = $request->input('id', [])
        )->update($data);

        return Redirect::back()->with(
            'message', __(':count categories have been updated.', ['count' => count($id)])
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
        $categories = CategoryProxy::query()->withTrashed()->whereIn(
            'id', $id = $request->input('id', [])
        );

        $request->has('force') ? $categories->forceDelete() : $categories->delete();

        return Redirect::back()->with(
            'message', __(':count categories have been deleted.', ['count' => count($id)])
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
        CategoryProxy::query()->onlyTrashed()->whereIn(
            'id', $id = $request->input('id', [])
        )->restore();

        return Redirect::back()->with(
            'message', __(':count categories have been restored.', ['count' => count($id)])
        );
    }
}
