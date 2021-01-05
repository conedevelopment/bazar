<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\User as User;
use Bazar\Http\Component;
use Bazar\Http\Requests\UserStoreRequest as StoreRequest;
use Bazar\Http\Requests\UserUpdateRequest as UpdateRequest;
use Bazar\Proxies\User as Proxy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Proxy::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,user')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Http\Component|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request) //: Component
    {
        $users = Proxy::query()->with('addresses')->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        return $request->expectsJson()
            ? Response::json($users)
            : Response::component('bazar::users.index', [
                'results' => $users,
                'filters' => Proxy::filters(),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Bazar\Http\Component
     */
    public function create(Request $request, User $user): Component
    {
        $user->setAttribute('addresses', [])->forceFill($request->old());

        return Response::component('bazar::users.create', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\UserStoreRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $user = Proxy::create($request->validated());

        return Redirect::route('bazar.users.show', $user)->with(
            'message', __('The user has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Bazar\Http\Component
     */
    public function show(User $user): Component
    {
        return Response::component('bazar::users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\UserUpdateRequest  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return Redirect::route('bazar.users.show', $user)->with(
            'message', __('The user has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return Redirect::back()->with(
                'error', __('The authenticated user cannot be deleted.')
            );
        }

        $user->trashed() ? $user->forceDelete() : $user->delete();

        return Redirect::route('bazar.users.index')->with(
            'message', __('The user has been deleted.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        return Redirect::back()->with(
            'message', __('The user has been restored.')
        );
    }
}
