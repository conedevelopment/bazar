<?php

namespace Bazar\Http\Controllers;

use Bazar\Bazar;
use Bazar\Http\Requests\OrderStoreRequest as StoreRequest;
use Bazar\Http\Requests\OrderUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Models\Address;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\User;
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
        if (Gate::getPolicyFor(Order::class)) {
            $this->authorizeResource(Order::class);
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
        $orders = Order::query()->with([
            'address', 'products', 'transactions', 'shipping',
        ])->filter($request)->latest()->paginate(
            $request->input('per_page')
        );

        return Component::render('Orders/Index', [
            'results' => $orders,
            'filters' => [
                'status' => Order::statuses(),
                'user' => User::pluck('name', 'id'),
            ],
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
        $order = Order::make()->setAttribute(
            'user', $request->old('user') ? User::make($request->old('user', [])) : null
        )->setRelation(
            'address', Address::make($request->old('address', []))
        )->setRelation(
            'products',
            Collection::make($request->old('products'))->map(function (array $product) {
                return Product::make()->forceFill($product);
            })
        );

        $order->shipping->fill($request->old('shipping', []))->setRelation(
            'address', Address::make($request->old('shipping.address', []))
        );

        return Component::render('Orders/Create', [
            'order' => $order,
            'countries' => Countries::all(),
            'statuses' => Order::statuses(),
            'currencies' => Bazar::currencies(),
            'action' => URL::route('bazar.orders.store'),
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

        $order = new Order($data = $request->validated());

        $order->user()->associate($data['user']['id'] ?? null)->save();
        $order->address->fill($data['address'])->save();
        $order->shipping->fill($data['shipping'])->save();
        $order->shipping->address->fill($data['shipping']['address'])->save();

        $products = Collection::make($data['products'])->mapWithKeys(function ($product) use ($data) {
            return [$product['id'] => [
                'tax' => $product['item_tax'] ?? 0,
                'quantity' => $product['item_quantity'] ?? 1,
                'price' => $product['prices'][$data['currency']]['normal'] ?? 0,
            ]];
        });

        $order->products()->attach($products);

        return Redirect::route('bazar.orders.show', $order)->with(
            'message', __('The order has been created.')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Order  $order
     * @return \Bazar\Http\Response
     */
    public function show(Order $order): Response
    {
        $order->loadMissing(['address', 'products', 'transactions', 'shipping', 'shipping.address']);

        return Component::render('Orders/Show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'action' => URL::route('bazar.orders.update', $order),
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

        return Redirect::route('bazar.orders.show', $order)->with(
            'message', __('The order has been updated.')
        );
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

        return Redirect::route('bazar.orders.index')->with(
            'message', __('The order has been deleted.')
        );
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

        return Redirect::back()->with(
            'message', __('The order has been restored.')
        );
    }
}
