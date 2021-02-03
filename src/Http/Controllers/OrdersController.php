<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Contracts\Models\Order;
use Bazar\Contracts\Models\Product;
use Bazar\Http\Requests\OrderStoreRequest as StoreRequest;
use Bazar\Http\Requests\OrderUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Proxies\Address as AddressProxy;
use Bazar\Proxies\Order as OrderProxy;
use Bazar\Proxies\Product as ProductProxy;
use Bazar\Proxies\User as UserProxy;
use Bazar\Support\Countries;
use Bazar\Support\Facades\Component;
use Bazar\Support\Facades\Discount;
use Bazar\Support\Facades\Shipping;
use Bazar\Support\Facades\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class OrdersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = OrderProxy::getProxiedClass())) {
            $this->authorizeResource($class);
            $this->middleware('can:update,order')->only('restore');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Bazar\Http\Response
     */
    public function index(Request $request): Response
    {
        $orders = OrderProxy::query()->with([
            'address', 'products', 'transactions', 'shipping',
        ])->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        return Component::render('Orders/Index', [
            'results' => $orders,
            'filters' => OrderProxy::filters(),
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
        $order = OrderProxy::make()->setAttribute(
            'user',
            UserProxy::make()->setAttribute('addresses', [])->forceFill($request->old('user', []))
        )->setRelation(
            'address',
            AddressProxy::make($request->old('address', []))
        )->setRelation(
            'products',
            Collection::make($request->old('products'))->map(function (array $product): Product {
                return ProductProxy::make()->forceFill($product);
            })
        );

        $order->shipping->fill($request->old('shipping', []))->setRelation(
            'address', AddressProxy::make($request->old('shipping.address', []))
        );

        return Component::render('Orders/Create', [
            'order' => $order,
            'countries' => Countries::all(),
            'statuses' => OrderProxy::statuses(),
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.orders.store', $order),
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

        $order = OrderProxy::make($data = $request->validated());

        $order->user()->associate($data['user']['id'] ?? null)->save();
        $order->address->fill($data['address'])->save();
        $order->shipping->fill($data['shipping'])->save();
        $order->shipping->address->fill($data['shipping']['address'])->save();

        $order->products()->attach(array_column($data['products'], 'item', 'id'));

        return Redirect::route('bazar.orders.show', $order)->with(
            'message', __('The order has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @return \Bazar\Http\Response
     */
    public function show(Order $order): Response
    {
        $order->loadMissing(['address', 'products', 'transactions', 'shipping', 'shipping.address']);

        return Component::render('Orders/Show', [
            'order' => $order,
            'statuses' => OrderProxy::statuses(),
            'action' => URL::route('bazar.orders.update', $order),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\OrderUpdateRequest  $request
     * @param  \Bazar\Contracts\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return Redirect::route('bazar.orders.show', $order)->with(
            'message', __('The order has been updated.')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->trashed() ? $order->forceDelete() : $order->delete();

        return Redirect::route('bazar.orders.index')->with(
            'message', __('The order has been deleted.')
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Bazar\Contracts\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Order $order): RedirectResponse
    {
        $order->restore();

        return Redirect::back()->with(
            'message', __('The order has been restored.')
        );
    }
}
