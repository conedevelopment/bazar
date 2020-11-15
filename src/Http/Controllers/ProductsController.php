<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Contracts\Models\Category;
use Bazar\Contracts\Models\Product;
use Bazar\Http\Requests\ProductStoreRequest as StoreRequest;
use Bazar\Http\Requests\ProductUpdateRequest as UpdateRequest;
use Bazar\Proxies\Category as CategoryProxy;
use Bazar\Proxies\Product as ProductProxy;
use Bazar\Support\Facades\Component;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = ProductProxy::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,product')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request) //: Response
    {
        $products = ProductProxy::query()->with('media')->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        return $request->expectsJson()
            ? Response::json($products)
            : Component::render('Products/Index', [
                'results' => $products,
                'filters' => ProductProxy::filters(),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(Request $request): Responsable
    {
        $product = ProductProxy::make()
            ->setAttribute('media', [])
            ->setAttribute('categories', [])
            ->forceFill($request->old());

        return Component::render('Products/Create', [
            'product' => $product,
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.products.store'),
            'categories' => CategoryProxy::query()->select(['id', 'name'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\ProductStoreRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $product = ProductProxy::create($request->validated());

        $product->categories()->attach(
            Arr::pluck($request->input('categories', []), 'id')
        );

        $product->media()->attach(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.show', $product)->with(
            'message', __('The product has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function show(Product $product): Responsable
    {
        $product->loadMissing(['media', 'categories:id,name']);

        return Component::render('Products/Show', [
            'product' => $product,
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.products.update', $product),
            'categories' => CategoryProxy::query()->select(['id', 'name'])->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\ProductUpdateRequest  $request
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        $product->categories()->sync(
            Arr::pluck($request->input('categories', []), 'id')
        );

        $product->media()->sync(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.products.show', $product)->with(
            'message', __('The product has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->trashed() ? $product->forceDelete() : $product->delete();

        return Redirect::route('bazar.products.index')->with(
            'message', __('The product has been deleted.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Product $product): RedirectResponse
    {
        $product->restore();

        return Redirect::back()->with(
            'message', __('The product has been restored.')
        );
    }
}
