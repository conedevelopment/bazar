<?php

namespace Bazar\Http\Controllers;

use Bazar\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class BatchProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor(Product::class)) {
            $this->middleware('can:batchUpdate,Bazar\Models\Product')->only('update');
            $this->middleware('can:batchDelete,Bazar\Models\Product')->only('destroy');
            $this->middleware('can:batchRestore,Bazar\Models\Product')->only('restore');
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
        $data = Arr::dot($request->except('ids'));

        $data = Collection::make($data)->filter()->mapWithKeys(static function ($item, $key) {
            return [str_replace('.', '->', $key) => $item];
        })->all();

        Product::query()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->update($data);

        return Redirect::back()->with(
            'message', __(':count products have been updated.', ['count' => count($ids)])
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
        $products = Product::withTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        );

        $request->has('force') ? $products->forceDelete() : $products->delete();

        return Redirect::back()->with(
            'message', __(':count products have been deleted.', ['count' => count($ids)])
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
        Product::onlyTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->restore();

        return Redirect::back()->with(
            'message', __(':count products have been restored.', ['count' => count($ids)])
        );
    }
}
