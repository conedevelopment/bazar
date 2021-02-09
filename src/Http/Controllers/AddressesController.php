<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\Address;
use Bazar\Contracts\Models\User;
use Bazar\Http\Requests\AddressStoreRequest as StoreRequest;
use Bazar\Http\Requests\AddressUpdateRequest as UpdateRequest;
use Inertia\Response;
use Bazar\Proxies\Address as AddressProxy;
use Bazar\Support\Countries;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class AddressesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = AddressProxy::getProxiedClass())) {
            $this->authorizeResource($class);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Inertia\Response
     */
    public function index(Request $request, User $user): Response
    {
        $addresses = $user->addresses()->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        return Inertia::render('Addresses/Index', [
            'user' => $user,
            'results' => $addresses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Inertia\Response
     */
    public function create(Request $request, User $user): Response
    {
        $address = AddressProxy::make($request->old());

        return Inertia::render('Addresses/Create', [
            'user' => $user,
            'address' => $address,
            'countries' => Countries::all(),
            'action' => URL::route('bazar.users.addresses.store', $user),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\AddressStoreRequest  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, User $user): RedirectResponse
    {
        $address = $user->addresses()->create($request->validated());

        return Redirect::route('bazar.users.addresses.show', [$user, $address])->with(
            'message', __('The address has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @param  \Bazar\Contracts\Models\Address  $address
     * @return \Inertia\Response
     */
    public function show(User $user, Address $address): Response
    {
        return Inertia::render('Addresses/Show', [
            'user' => $user,
            'address' => $address,
            'countries' => Countries::all(),
            'action' => URL::route('bazar.users.addresses.update', [$user, $address]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\AddressUpdateRequest  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @param  \Bazar\Contracts\Models\Address  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, User $user, Address $address): RedirectResponse
    {
        $address->update($request->validated());

        return Redirect::route('bazar.users.addresses.show', [$user, $address])->with(
            'message', __('The address has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @param  \Bazar\Contracts\Models\Address  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user, Address $address): RedirectResponse
    {
        $address->delete();

        return Redirect::route('bazar.users.addresses.index', $user)->with(
            'message', __('The address has been deleted.')
        );
    }
}
