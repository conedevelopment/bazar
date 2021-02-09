<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\Category;
use Bazar\Http\Requests\CategoryStoreRequest as StoreRequest;
use Bazar\Http\Requests\CategoryUpdateRequest as UpdateRequest;
use Inertia\Response;
use Bazar\Proxies\Category as CategoryProxy;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = CategoryProxy::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,category')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $categories = CategoryProxy::query()->with('media')->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        return Inertia::render('Categories/Index', [
            'results' => $categories,
            'filters' => CategoryProxy::filters(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request): Response
    {
        $category = CategoryProxy::make()
            ->setAttribute('media', [])
            ->forceFill($request->old());

        return Inertia::render('Categories/Create', [
            'category' => $category,
            'action' => URL::route('bazar.categories.store'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\CategoryStoreRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $category = CategoryProxy::create($request->validated());

        $category->media()->attach(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.categories.show', $category)->with(
            'message', __('The category has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\Category  $category
     * @return \Inertia\Response
     */
    public function show(Category $category): Response
    {
        $category->loadMissing('media');

        return Inertia::render('Categories/Show', [
            'category' => $category,
            'action' => URL::route('bazar.categories.update', $category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\CategoryUpdateRequest  $request
     * @param  \Bazar\Contracts\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        $category->media()->sync(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.categories.show', $category)->with(
            'message', __('The category has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Contracts\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->trashed() ? $category->forceDelete() : $category->delete();

        return Redirect::route('bazar.categories.index')->with(
            'message', __('The category has been deleted.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Contracts\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Category $category): RedirectResponse
    {
        $category->restore();

        return Redirect::back()->with(
            'message', __('The category has been restored.')
        );
    }
}
