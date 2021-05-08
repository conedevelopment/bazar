<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\CategoryStoreRequest as StoreRequest;
use Bazar\Http\Requests\CategoryUpdateRequest as UpdateRequest;
use Bazar\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class CategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Category::getProxiedClass())) {
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
        $categories = Category::proxy()
                        ->newQuery()
                        ->with('media')
                        ->filter($request)
                        ->latest()
                        ->paginate($request->input('per_page'));

        return Inertia::render('Categories/Index', [
            'response' => $categories,
            'filters' => Category::proxy()::filters($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        $category = Category::proxy()->newInstance()->setAttribute('media', []);

        return Inertia::render('Categories/Create', [
            'category' => $category,
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
        $category = Category::proxy()->newQuery()->create($request->validated());

        $category->media()->attach(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.categories.show', $category)
                        ->with('message', __('The category has been created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Category  $category
     * @return \Inertia\Response
     */
    public function show(Category $category): Response
    {
        $category->loadMissing('media');

        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\CategoryUpdateRequest  $request
     * @param  \Bazar\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        $category->media()->sync(
            Arr::pluck($request->input('media', []), 'id')
        );

        return Redirect::route('bazar.categories.show', $category)
                        ->with('message', __('The category has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->trashed() ? $category->forceDelete() : $category->delete();

        return Redirect::route('bazar.categories.index')
                        ->with('message', __('The category has been deleted.'));
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Category $category): RedirectResponse
    {
        $category->restore();

        return Redirect::back()
                        ->with('message', __('The category has been restored.'));
    }
}
