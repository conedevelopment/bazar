<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Http\Requests\OrderStoreRequest as StoreRequest;
use Bazar\Http\Requests\OrderUpdateRequest as UpdateRequest;
use Bazar\Models\Address;
use Bazar\Models\Order;
use Bazar\Models\User;
use Bazar\Support\Countries;
use Bazar\Support\Facades\Discount;
use Bazar\Support\Facades\Shipping;
use Bazar\Support\Facades\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class OrdersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Order::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,order')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $orders = Order::proxy()
                    ->newQuery()
                    ->with(['address', 'products', 'transactions', 'shipping'])
                    ->filter($request)
                    ->latest()
                    ->paginate($request->input('per_page'));

        return Inertia::render('Orders/Index', [
            'response' => $orders,
            'filters' => Order::proxy()::filters(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        $order = Order::proxy()
                    ->newInstance()
                    ->setAttribute('user', User::proxy()->newInstance()->setAttribute('addresses', []))
                    ->setRelation('address', Address::proxy()->newInstance());

        $order->shipping->setRelation('address', Address::proxy()->newInstance());

        return Inertia::render('Orders/Create', [
            'order' => $order,
            'countries' => Countries::all(),
            'currencies' => Bazar::currencies(),
            'statuses' => Order::proxy()::statuses(),
            'drivers' => Collection::make(Shipping::enabled())->map->name(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\OrderStoreRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        Tax::disable();
        Discount::disable();

        $order = Order::proxy()->newInstance($data = $request->validated());

        $order->user()->associate($data['user']['id'] ?? null)->save();
        $order->address->fill($data['address'])->save();
        $order->shipping->fill($data['shipping'])->save();
        $order->shipping->address->fill($data['shipping']['address'])->save();

        $order->products()->attach(array_column($data['products'], 'item', 'id'));

        return Redirect::route('bazar.orders.show', $order)
                        ->with('message', __('The order has been created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Order  $order
     * @return \Inertia\Response
     */
    public function show(Order $order): Response
    {
        $order->loadMissing(['address', 'products', 'transactions', 'shipping', 'shipping.address']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'statuses' => Order::proxy()::statuses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\OrderUpdateRequest  $request
     * @param  \Bazar\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return Redirect::route('bazar.orders.show', $order)
                        ->with('message', __('The order has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->trashed() ? $order->forceDelete() : $order->delete();

        return Redirect::route('bazar.orders.index')
                        ->with('message', __('The order has been deleted.'));
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Order $order): RedirectResponse
    {
        $order->restore();

        return Redirect::back()
                        ->with('message', __('The order has been restored.'));
    }
}
