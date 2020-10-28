<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Http\Requests\VariationStoreRequest as StoreRequest;
use Bazar\Http\Requests\VariationUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Models\Product;
use Bazar\Models\Variation;
use Bazar\Support\Facades\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class VariationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor(Variation::class)) {
            $this->authorizeResource(Variation::class);
            $this->middleware('can:update,variation')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Bazar\Http\Response
     */
    public function index(Request $request, Product $product): Response
    {
        $variations = $product->variations()->with('media')->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        $variations->getCollection()->each(function (Variation $variation) use ($product) {
            $variation->setRelation(
                'product', $product->withoutRelations()->makeHidden('variations')
            );
        });

        return Component::render('Variation/Index', [
            'product' => $product,
            'results' => $variations,
            'filters' => Variation::filters(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Bazar\Http\Response
     */
    public function create(Request $request, Product $product): Response
    {
        $variation = Variation::make($request->old())
            ->setAttribute('media', [])
            ->setRelation(
                'product', $product->withoutRelations()->makeHidden('variation')
            );

        return Component::render('Variation/Create', [
            'product' => $product,
            'variation' => $variation,
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.products.variations.store', $product),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\VariationStoreRequest  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, Product $product): RedirectResponse
    {
        $variation = $product->variations()->create($request->validated());

        $variation->media()->attach(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.variations.show', [$product, $variation])->with(
            'message', __('The variation has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variation  $variation
     * @return \Bazar\Http\Response
     */
    public function show(Product $product, Variation $variation): Response
    {
        $variation->setRelation(
            'product', $product->withoutRelations()->makeHidden('variation')
        )->loadMissing('media');

        return Component::render('Variation/Show', [
            'product' => $product,
            'variation' => $variation,
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.products.variations.update', [$product, $variation]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\VariationUpdateRequest  $request
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variation  $variation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Product $product, Variation $variation): RedirectResponse
    {
        $variation->update($request->validated());

        $variation->media()->sync(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.variations.show', [$product, $variation])->with(
            'message', __('The variation has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variation  $variation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product, Variation $variation): RedirectResponse
    {
        $variation->trashed() ? $variation->forceDelete() : $variation->delete();

        return Redirect::route('bazar.products.variations.index', $product)->with(
            'message', __('The variation has been deleted.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  \Bazar\Models\Variation  $variation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Product $product, Variation $variation): RedirectResponse
    {
        $variation->restore();

        return Redirect::back()->with(
            'message', __('The variation has been restored.')
        );
    }
}
