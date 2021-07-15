<?php

namespace Cone\Bazar\Http\Controllers\Batch;

use Cone\Bazar\Http\Controllers\Controller;
use Cone\Bazar\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Product::getProxiedClass())) {
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

        Product::proxy()
            ->newQuery()
            ->whereIn('id', $id = $request->input('id', []))
            ->update($data);

        return Redirect::back()
                        ->with('message', __(':count products have been updated.', ['count' => count($id)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $products = Product::proxy()
                        ->newQuery()
                        ->withTrashed()
                        ->whereIn('id', $id = $request->input('id', []));

        $request->has('force') ? $products->forceDelete() : $products->delete();

        return Redirect::back()
                        ->with('message', __(':count products have been deleted.', ['count' => count($id)]));
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request): RedirectResponse
    {
        Product::proxy()
            ->newQuery()
            ->onlyTrashed()
            ->whereIn('id', $id = $request->input('id', []))
            ->restore();

        return Redirect::back()
                        ->with('message', __(':count products have been restored.', ['count' => count($id)]));
    }
}
