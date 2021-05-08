<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\UserStoreRequest as StoreRequest;
use Bazar\Http\Requests\UserUpdateRequest as UpdateRequest;
use Bazar\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = User::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,user')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request) //: Response
    {
        $users = User::proxy()
                    ->newQuery()
                    ->with('addresses')
                    ->filter($request)
                    ->latest()
                    ->paginate($request->input('per_page'));

        return $request->expectsJson()
            ? ResponseFactory::json($users)
            : Inertia::render('Users/Index', [
                'response' => $users,
                'filters' => User::proxy()::filters($request),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Bazar\Models\User  $user
     * @return \Inertia\Response
     */
    public function create(User $user): Response
    {
        $user->setAttribute('addresses', []);

        return Inertia::render('Users/Create', [
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
        $user = User::proxy()->newQuery()->create($request->validated());

        return Redirect::route('bazar.users.show', $user)
                        ->with('message', __('The user has been created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\User  $user
     * @return \Inertia\Response
     */
    public function show(User $user): Response
    {
        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\UserUpdateRequest  $request
     * @param  \Bazar\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return Redirect::route('bazar.users.show', $user)
                        ->with('message', __('The user has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return Redirect::back()->with('message', __('The authenticated user cannot be deleted.'));
        }

        $user->trashed() ? $user->forceDelete() : $user->delete();

        return Redirect::route('bazar.users.index')
                        ->with('message', __('The user has been deleted.'));
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        return Redirect::back()
                        ->with('message', __('The user has been restored.'));
    }
}
