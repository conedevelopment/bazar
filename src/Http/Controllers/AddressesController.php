<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\AddressStoreRequest as StoreRequest;
use Bazar\Http\Requests\AddressUpdateRequest as UpdateRequest;
use Bazar\Models\Address;
use Bazar\Models\User;
use Bazar\Support\Countries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class AddressesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Address::getProxiedClass())) {
            $this->authorizeResource($class);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\User  $user
     * @return \Inertia\Response
     */
    public function index(Request $request, User $user): Response
    {
        $addresses = $user->addresses()
                        ->filter($request)
                        ->latest()
                        ->paginate($request->input('per_page'))
                        ->withQueryString();

        return Inertia::render('Addresses/Index', [
            'user' => $user,
            'response' => $addresses,
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
        return Inertia::render('Addresses/Create', [
            'user' => $user,
            'countries' => Countries::all(),
            'address' => Address::proxy()->newInstance(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\AddressStoreRequest  $request
     * @param  \Bazar\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, User $user): RedirectResponse
    {
        $address = $user->addresses()->create($request->validated());

        return Redirect::route('bazar.users.addresses.show', [$user, $address])
                        ->with('message', __('The address has been created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\User  $user
     * @param  \Bazar\Models\Address  $address
     * @return \Inertia\Response
     */
    public function show(User $user, Address $address): Response
    {
        return Inertia::render('Addresses/Show', [
            'user' => $user,
            'address' => $address,
            'countries' => Countries::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\AddressUpdateRequest  $request
     * @param  \Bazar\Models\User  $user
     * @param  \Bazar\Models\Address  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, User $user, Address $address): RedirectResponse
    {
        $address->update($request->validated());

        return Redirect::route('bazar.users.addresses.show', [$user, $address])
                        ->with('message', __('The address has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\User  $user
     * @param  \Bazar\Models\Address  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user, Address $address): RedirectResponse
    {
        $address->delete();

        return Redirect::route('bazar.users.addresses.index', $user)
                        ->with('message', __('The address has been deleted.'));
    }
}
