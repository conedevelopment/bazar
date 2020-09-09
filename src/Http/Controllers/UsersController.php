<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\User as User;
use Bazar\Filters\Filters;
use Bazar\Http\Requests\UserStoreRequest as StoreRequest;
use Bazar\Http\Requests\UserUpdateRequest as UpdateRequest;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Bazar\Support\Facades\Component;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        if (Gate::getPolicyFor(get_class($user))) {
            $this->authorizeResource(get_class($user));
            $this->middleware('can:update,user')->only('restore');
        }
    }

/**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function index(Request $request, User $user)// : Responsable
    {
        $users = $user->newQuery()->with('addresses')->filter(
            $request,
            $filters = Filters::make(User::class)->searchIn(['name', 'email'])
        )->paginate($request->input('per_page', 25));

        return ! $request->bazar() ? Component::render('Users/Index', [
            'results' => $users,
            'filters' => $filters->options(),
        ]) : Response::json($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(Request $request, User $user): Responsable
    {
        $user->setAttribute('addresses', [])
            ->forceFill($request->old());

        return Component::render('Users/Create', [
            'user' => $user,
            'action' => URL::route('bazar.users.store'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\UserStoreRequest  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, User $user): RedirectResponse
    {
        $user = $user->newQuery()->create($request->validated());

        return Redirect::route('bazar.users.show', $user)->with(
            'message', __('The user has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function show(User $user): Responsable
    {
        return Component::render('Users/Show', [
            'user' => $user,
            'action' => URL::route('bazar.users.update', $user),
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
                'message', __('The authenticated user cannot be deleted.')
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
