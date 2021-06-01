<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Http\Requests\VariantStoreRequest as StoreRequest;
use Bazar\Http\Requests\VariantUpdateRequest as UpdateRequest;
use Bazar\Models\Product;
use Bazar\Models\Variant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class VariantsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Variant::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,variant')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Inertia\Response
     */
    public function index(Request $request, Product $product): Response
    {
        $variants = $product->variants()
                        ->with('media')
                        ->filter($request)
                        ->latest()
                        ->paginate($request->input('per_page'))
                        ->withQueryString();

        $variants->getCollection()->each(static function (Variant $variant) use ($product): void {
            $variant->setRelation('product', $product->withoutRelations()->makeHidden('variants'));
        });

        return Inertia::render('Variants/Index', [
            'response' => $variants,
            'filters' => Variant::proxy()::filters($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Bazar\Models\Product  $product
     * @return \Inertia\Response
     */
    public function create(Product $product): Response
    {
        $variant = Variant::proxy()
                    ->newInstance()
                    ->setAttribute('media', [])
                    ->setRelation('product', $product->withoutRelations()->makeHidden('variant'));

        return Inertia::render('Variants/Create', [
            'product' => $product,
            'variant' => $variant,
            'currencies' => Bazar::getCurrencies(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\VariantStoreRequest  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, Product $product): RedirectResponse
    {
        $variant = $product->variants()->create($request->validated());

        $variant->media()->attach(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.variants.show', [$product, $variant])
                        ->with('message', __('The variant has been created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variant  $variant
     * @return \Inertia\Response
     */
    public function show(Product $product, Variant $variant): Response
    {
        $variant->setRelation('product', $product->withoutRelations()->makeHidden('variant'))
                ->loadMissing('media');

        return Inertia::render('Variants/Show', [
            'product' => $product,
            'variant' => $variant,
            'currencies' => Bazar::getCurrencies(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\VariantUpdateRequest  $request
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Product $product, Variant $variant): RedirectResponse
    {
        $variant->update($request->validated());

        $variant->media()->sync(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.variants.show', [$product, $variant])
                        ->with('message', __('The variant has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product, Variant $variant): RedirectResponse
    {
        $variant->trashed() ? $variant->forceDelete() : $variant->delete();

        return Redirect::route('bazar.products.variants.index', $product)
                        ->with('message', __('The variant has been deleted.'));
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Product $product, Variant $variant): RedirectResponse
    {
        $variant->restore();

        return Redirect::back()
                        ->with('message', __('The variant has been restored.'));
    }
}
