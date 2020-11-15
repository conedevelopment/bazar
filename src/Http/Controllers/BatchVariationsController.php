<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\Product;
use Bazar\Proxies\Variation as VariationProxy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class BatchVariationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = VariationProxy::getProxiedClass())) {
            $this->middleware("can:batchUpdate,{$class}")->only('update');
            $this->middleware("can:batchDelete,{$class}")->only('destroy');
            $this->middleware("can:batchRestore,{$class}")->only('restore');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = Arr::dot($request->except('ids'));

        $data = Collection::make($data)->filter()->mapWithKeys(static function ($item, string $key): array {
            return [str_replace('.', '->', $key) => $item];
        })->all();

        $product->variations()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->update($data);

        return Redirect::back()->with(
            'message', __(':count variations have been updated.', ['count' => count($ids)])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $variations = $product->variations()->withTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        );

        $request->has('force') ? $variations->forceDelete() : $variations->delete();

        return Redirect::back()->with(
            'message', __(':count variations have been deleted.', ['count' => count($ids)])
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request, Product $product): RedirectResponse
    {
        $product->variations()->onlyTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->restore();

        return Redirect::back()->with(
            'message', __(':count variations have been restored.', ['count' => count($ids)])
        );
    }
}
