<?php

namespace Bazar\Http\Controllers;

use Bazar\Filters\Filters;
use Bazar\Http\Requests\CategoryStoreRequest as StoreRequest;
use Bazar\Http\Requests\CategoryUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Models\Category;
use Bazar\Support\Facades\Component;
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
        if (Gate::getPolicyFor(Category::class)) {
            $this->authorizeResource(Category::class);
            $this->middleware('can:update,category')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Http\Response
     */
    public function index(Request $request): Response
    {
        $categories = Category::query()->with('media')->filter(
            $request,
            $filters = Filters::make(Category::class)->searchIn('name')
        )->paginate($request->input('per_page', 25));

        return Component::render('Categories/Index', [
            'results' => $categories,
            'filters' => $filters->options(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Http\Response
     */
    public function create(Request $request): Response
    {
        $category = Category::make()
            ->setAttribute('media', [])
            ->forceFill($request->old());

        return Component::render('Categories/Create', [
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
        $category = Category::create($request->validated());

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
     * @param  \Bazar\Models\Category  $category
     * @return \Bazar\Http\Response
     */
    public function show(Category $category): Response
    {
        $category->loadMissing('media');

        return Component::render('Categories/Show', [
            'category' => $category,
            'action' => URL::route('bazar.categories.update', $category),
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

        return Redirect::route('bazar.categories.show', $category)->with(
            'message', __('The category has been updated.')
        );
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

        return Redirect::route('bazar.categories.index')->with(
            'message', __('The category has been deleted.')
        );
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

        return Redirect::back()->with(
            'message', __('The category has been restored.')
        );
    }
}
