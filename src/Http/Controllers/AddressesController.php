<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\User;
use Bazar\Filters\Filters;
use Bazar\Http\Requests\AddressStoreRequest as StoreRequest;
use Bazar\Http\Requests\AddressUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Models\Address;
use Bazar\Support\Countries;
use Bazar\Support\Facades\Component;
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
        if (Gate::getPolicyFor(Address::class)) {
            $this->authorizeResource(Address::class);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Bazar\Http\Response
     */
    public function index(Request $request, User $user): Response
    {
        $addresses = $user->addresses()->filter(
            $request,
            $filters = Filters::make(Address::class)->searchIn('alias')
        )->latest()->paginate($request->input('per_page'));

        return Component::render('Addresses/Index', [
            'results' => $addresses,
            'filters' => $filters->options(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return \Bazar\Http\Response
     */
    public function create(Request $request, User $user): Response
    {
        $address = Address::make($request->old());

        return Component::render('Addresses/Create', [
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
     * @param  \Bazar\Models\Address  $address
     * @return \Bazar\Http\Response
     */
    public function show(User $user, Address $address): Response
    {
        return Component::render('Addresses/Show', [
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
     * @param  \Bazar\Models\Address  $address
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
     * @param  \Bazar\Models\Address  $address
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
