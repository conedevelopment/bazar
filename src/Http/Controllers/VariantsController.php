<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Contracts\Models\Product;
use Bazar\Contracts\Models\Variant;
use Bazar\Http\Requests\VariantStoreRequest as StoreRequest;
use Bazar\Http\Requests\VariantUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Proxies\Variant as VariantProxy;
use Bazar\Support\Facades\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class VariantsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = VariantProxy::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,variant')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Bazar\Http\Response
     */
    public function index(Request $request, Product $product): Response
    {
        $variants = $product->variants()->with('media')->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        $variants->getCollection()->each(static function (Variant $variant) use ($product): void {
            $variant->setRelation(
                'product', $product->withoutRelations()->makeHidden('variants')
            );
        });

        return Component::render('Variants/Index', [
            'product' => $product,
            'results' => $variants,
            'filters' => VariantProxy::filters(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Bazar\Http\Response
     */
    public function create(Request $request, Product $product): Response
    {
        $variant = VariantProxy::make($request->old())
            ->setAttribute('media', [])
            ->setRelation(
                'product', $product->withoutRelations()->makeHidden('variant')
            );

        return Component::render('Variants/Create', [
            'product' => $product,
            'variant' => $variant,
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.products.variants.store', $product),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\VariantStoreRequest  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, Product $product): RedirectResponse
    {
        $variant = $product->variants()->create($request->validated());

        $variant->media()->attach(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.variants.show', [$product, $variant])->with(
            'message', __('The variant has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  \Bazar\Contracts\Models\Variant  $variant
     * @return \Bazar\Http\Response
     */
    public function show(Product $product, Variant $variant): Response
    {
        $variant->setRelation(
            'product', $product->withoutRelations()->makeHidden('variant')
        )->loadMissing('media');

        return Component::render('Variants/Show', [
            'product' => $product,
            'variant' => $variant,
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.products.variants.update', [$product, $variant]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\VariantUpdateRequest  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  \Bazar\Contracts\Models\Variant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Product $product, Variant $variant): RedirectResponse
    {
        $variant->update($request->validated());

        $variant->media()->sync(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.variants.show', [$product, $variant])->with(
            'message', __('The variant has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  \Bazar\Contracts\Models\Variant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product, Variant $variant): RedirectResponse
    {
        $variant->trashed() ? $variant->forceDelete() : $variant->delete();

        return Redirect::route('bazar.products.variants.index', $product)->with(
            'message', __('The variant has been deleted.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  \Bazar\Contracts\Models\Variant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Product $product, Variant $variant): RedirectResponse
    {
        $variant->restore();

        return Redirect::back()->with(
            'message', __('The variant has been restored.')
        );
    }
}
